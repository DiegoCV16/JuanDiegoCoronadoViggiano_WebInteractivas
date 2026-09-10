/* =========================================================
   MovieReviews - lógica compartida entre index.html y reseña.html
   ========================================================= */

const MR_CONFIG = {
  // Intentamos primero contra la API pública de IMDb (api.imdbapi.dev).
  // Si el navegador no puede alcanzarla (CORS, sin conexión, etc.)
  // se hace fallback automático a peliculas.json.
  API_LIST_URL: "https://api.imdbapi.dev/titles?types=MOVIE&sortBy=SORT_BY_POPULARITY",
  API_DETAIL_URL: (id) => `https://api.imdbapi.dev/titles/${id}`,
  LOCAL_JSON_URL: "peliculas.json",
  FAVORITES_KEY: "mr_favoritos",
};

/* ---------------------------------------------------------
   FAVORITOS (persistidos en localStorage, compartidos entre páginas)
   --------------------------------------------------------- */
const MRFavorites = {
  getAll() {
    try {
      const raw = localStorage.getItem(MR_CONFIG.FAVORITES_KEY);
      return raw ? JSON.parse(raw) : [];
    } catch (e) {
      return [];
    }
  },

  isFavorite(id) {
    return this.getAll().includes(id);
  },

  toggle(id) {
    let favs = this.getAll();
    if (favs.includes(id)) {
      favs = favs.filter((f) => f !== id);
    } else {
      favs.push(id);
    }
    localStorage.setItem(MR_CONFIG.FAVORITES_KEY, JSON.stringify(favs));
    $(document).trigger("mr:favoritesChanged");
    return favs.includes(id);
  },

  remove(id) {
    const favs = this.getAll().filter((f) => f !== id);
    localStorage.setItem(MR_CONFIG.FAVORITES_KEY, JSON.stringify(favs));
    $(document).trigger("mr:favoritesChanged");
  },

  clear() {
    localStorage.setItem(MR_CONFIG.FAVORITES_KEY, JSON.stringify([]));
    $(document).trigger("mr:favoritesChanged");
  },

  count() {
    return this.getAll().length;
  },
};

/* Actualiza el contador de favoritos en el navbar (presente en ambas páginas) */
function mrUpdateFavCounter() {
  $(".mr-fav-count").text(MRFavorites.count());
}

/* ---------------------------------------------------------
   HELPERS DE PRESENTACIÓN
   --------------------------------------------------------- */

// Genera HTML de 10 estrellas según la calificación (0-10)
function mrStarsHtml(rating) {
  const r = Math.round(rating || 0);
  let html = "";
  for (let i = 1; i <= 10; i++) {
    html += i <= r ? '<i class="bi bi-star-fill"></i>' : '<i class="bi bi-star"></i>';
  }
  return html;
}

function mrFormatMoney(value) {
  if (value === null || value === undefined) return "N/D";
  if (value >= 1000000) return "$" + (value / 1000000).toFixed(1).replace(/\.0$/, "") + "M";
  if (value >= 1000) return "$" + (value / 1000).toFixed(0) + "K";
  return "$" + value;
}

function mrDecadeOf(year) {
  if (!year) return null;
  if (year >= 1990 && year <= 1999) return "1990-1999";
  if (year >= 2000 && year <= 2009) return "2000-2009";
  if (year >= 2010 && year <= 2019) return "2010-2019";
  if (year >= 2020 && year <= 2026) return "2020-2026";
  return "otros";
}

/* ---------------------------------------------------------
   CARGA DE DATOS: intenta la API de IMDb, si falla usa el JSON local
   --------------------------------------------------------- */

// Normaliza un registro venga de donde venga (API o JSON local) para
// que siempre tenga las mismas llaves esperadas por la UI.
function mrNormalizeMovie(m) {
  return {
    id: m.id,
    primaryTitle: m.primaryTitle || m.originalTitle || "Sin título",
    primaryImage: m.primaryImage || null,
    startYear: m.startYear || null,
    runtimeMinutes: m.runtimeMinutes || null,
    genres: m.genres || [],
    averageRating: m.averageRating || 0,
    numVotes: m.numVotes || 0,
    metascore: m.metascore ?? null,
    description: m.description || "Sin sinopsis disponible.",
    spokenLanguages: m.spokenLanguages || [],
    countriesOfOrigin: m.countriesOfOrigin || [],
    interests: m.interests || [],
    budget: m.budget ?? null,
    grossWorldwide: m.grossWorldwide ?? null,
    contentRating: m.contentRating || "N/D",
    productionCompanies: (m.productionCompanies || []).map((c) => (typeof c === "string" ? c : c.name)),
    externalLinks: m.externalLinks || [],
    trailer: m.trailer || null,
  };
}

// Carga el listado completo de películas.
// onSuccess(peliculas, origen) donde origen es "api" o "local"
// onError() se llama solo si ambas fuentes fallan
function mrLoadMovies(onSuccess, onError) {
  $.ajax({
    url: MR_CONFIG.API_LIST_URL,
    method: "GET",
    dataType: "json",
    timeout: 6000,
  })
    .done(function (data) {
      const list = data && (data.titles || data.results || data);
      if (Array.isArray(list) && list.length) {
        onSuccess(list.map(mrNormalizeMovie), "api");
      } else {
        mrLoadLocalMovies(onSuccess, onError);
      }
    })
    .fail(function () {
      // La API de IMDb no respondió (CORS, sin conexión, límite, etc.)
      mrLoadLocalMovies(onSuccess, onError);
    });
}

function mrLoadLocalMovies(onSuccess, onError) {
  $.getJSON(MR_CONFIG.LOCAL_JSON_URL)
    .done(function (data) {
      if (Array.isArray(data) && data.length) {
        onSuccess(data.map(mrNormalizeMovie), "local");
      } else {
        onError();
      }
    })
    .fail(function () {
      onError();
    });
}

// Carga una única película por id: intenta la API, luego el JSON local.
function mrLoadMovieById(id, onSuccess, onError) {
  $.ajax({
    url: MR_CONFIG.API_DETAIL_URL(id),
    method: "GET",
    dataType: "json",
    timeout: 6000,
  })
    .done(function (data) {
      if (data && data.id) {
        onSuccess(mrNormalizeMovie(data), "api");
      } else {
        mrFindLocalMovieById(id, onSuccess, onError);
      }
    })
    .fail(function () {
      mrFindLocalMovieById(id, onSuccess, onError);
    });
}

function mrFindLocalMovieById(id, onSuccess, onError) {
  $.getJSON(MR_CONFIG.LOCAL_JSON_URL)
    .done(function (data) {
      const found = Array.isArray(data) ? data.find((m) => m.id === id) : null;
      if (found) {
        onSuccess(mrNormalizeMovie(found), "local");
      } else {
        onError();
      }
    })
    .fail(function () {
      onError();
    });
}

/* ---------------------------------------------------------
   MODAL DE FAVORITOS (markup idéntico en index.html y reseña.html)
   --------------------------------------------------------- */

// Guardamos en memoria la última lista completa cargada para poder
// pintar el modal de favoritos sin volver a pedir datos.
let MR_ALL_MOVIES_CACHE = [];

function mrSetMoviesCache(list) {
  MR_ALL_MOVIES_CACHE = list;
}

function mrRenderFavoritesModal() {
  const favIds = MRFavorites.getAll();
  const $count = $("#mr-fav-modal-count");
  $count.text(favIds.length);

  const $body = $("#mr-fav-modal-body");
  $body.empty();

  if (!favIds.length) {
    $body.html(
      '<div class="mr-fav-empty"><i class="bi bi-heart"></i>' +
        "<p class='mb-0'>No tienes películas favoritas</p>" +
        "<p class='mb-0 small'>Agrega algunas desde la página de inicio</p></div>"
    );
    return;
  }

  const favMovies = MR_ALL_MOVIES_CACHE.filter((m) => favIds.includes(m.id));
  const $row = $('<div class="row g-3"></div>');

  favMovies.forEach((m) => {
    const col = $(`
      <div class="col-6 col-md-4 mr-fav-item" data-id="${m.id}">
        <img src="${m.primaryImage || "https://placehold.co/300x450?text=Sin+imagen"}" alt="${m.primaryTitle}">
        <div class="fw-semibold small mt-1 text-truncate">${m.primaryTitle}</div>
        <div class="d-flex justify-content-between small text-muted mb-1">
          <span>${m.startYear || "N/D"}</span>
          <span>${m.averageRating ? m.averageRating + " ★" : "N/D"}</span>
        </div>
        <div class="d-flex gap-1">
          <a href="reseña.html?id=${m.id}" class="btn btn-sm mr-btn-primary text-white flex-grow-1">Ver</a>
          <button class="btn btn-sm btn-outline-danger mr-fav-remove" data-id="${m.id}" title="Eliminar de favoritos">
            <i class="bi bi-x"></i>
          </button>
        </div>
      </div>
    `);
    $row.append(col);
  });

  $body.append($row);
}

// Wiring común del modal: abrir, quitar uno, quitar todos
$(function () {
  mrUpdateFavCounter();
  $(document).on("mr:favoritesChanged", function () {
    mrUpdateFavCounter();
    if ($("#mr-fav-modal").hasClass("show")) {
      mrRenderFavoritesModal();
    }
  });

  $("#mr-fav-modal").on("show.bs.modal", mrRenderFavoritesModal);

  $(document).on("click", ".mr-fav-remove", function () {
    const id = $(this).data("id");
    MRFavorites.remove(id);
  });

  $("#mr-fav-clear-all").on("click", function () {
    MRFavorites.clear();
  });
});
