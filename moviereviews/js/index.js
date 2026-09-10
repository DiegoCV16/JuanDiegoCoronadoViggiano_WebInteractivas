/* =========================================================
   MovieReviews - index.html
   ========================================================= */

let mrCurrentMovies = [];   // todas las películas cargadas
let mrCurrentDecade = "Todos";
let mrCurrentSearch = "";

$(function () {
  mrFetchAndRender();

  $("#mr-retry-btn").on("click", mrFetchAndRender);

  // --- Filtro por década ---
  $("#mr-filters").on("click", ".mr-filter-btn", function () {
    $(".mr-filter-btn").removeClass("active");
    $(this).addClass("active");
    mrCurrentDecade = $(this).data("decade");
    mrApplyFiltersAndRender();
  });

  // --- Búsqueda en tiempo real ---
  $("#mr-search").on("keyup", function () {
    mrCurrentSearch = $(this).val().trim().toLowerCase();
    mrApplyFiltersAndRender();
  });

  // --- Toggle de favorito desde una card ---
  $("#mr-movies-grid").on("click", ".mr-fav-toggle", function (e) {
    e.preventDefault();
    const id = $(this).data("id");
    const isFav = MRFavorites.toggle(id);
    $(this).toggleClass("is-fav", isFav);
    $(this).find("i").toggleClass("bi-heart-fill", isFav).toggleClass("bi-heart", !isFav);
  });

  // --- Efecto hover con jQuery (elevar la card) ---
  $("#mr-movies-grid").on("mouseenter", ".mr-card", function () {
    $(this).css({ transform: "translateY(-6px)", boxShadow: "0 10px 22px rgba(0,0,0,0.15)" });
  });
  $("#mr-movies-grid").on("mouseleave", ".mr-card", function () {
    $(this).css({ transform: "translateY(0)", boxShadow: "0 1px 3px rgba(0,0,0,0.08)" });
  });
});

function mrFetchAndRender() {
  $("#mr-error, #mr-empty").addClass("d-none");
  $("#mr-movies-grid").empty();
  $("#mr-loading").removeClass("d-none");

  mrLoadMovies(
    function (movies) {
      $("#mr-loading").addClass("d-none");
      mrCurrentMovies = movies;
      mrSetMoviesCache(movies);
      mrApplyFiltersAndRender();
    },
    function () {
      $("#mr-loading").addClass("d-none");
      $("#mr-error").removeClass("d-none");
    }
  );
}

function mrApplyFiltersAndRender() {
  let filtered = mrCurrentMovies;

  if (mrCurrentDecade !== "Todos") {
    filtered = filtered.filter((m) => mrDecadeOf(m.startYear) === mrCurrentDecade);
  }

  if (mrCurrentSearch) {
    filtered = filtered.filter((m) => m.primaryTitle.toLowerCase().includes(mrCurrentSearch));
  }

  mrRenderGrid(filtered);
}

function mrRenderGrid(movies) {
  const $grid = $("#mr-movies-grid");
  $grid.empty();

  if (!movies.length) {
    $("#mr-empty").removeClass("d-none");
    return;
  }
  $("#mr-empty").addClass("d-none");

  movies.forEach((m) => {
    const isFav = MRFavorites.isFavorite(m.id);
    const poster = m.primaryImage || "https://placehold.co/400x600?text=Sin+imagen";

    const $col = $(`
      <div class="col-12 col-md-6 col-lg-3">
        <div class="mr-card">
          <div class="mr-card-poster-wrap">
            <img src="${poster}" alt="${m.primaryTitle}" loading="lazy">
            <button class="mr-fav-toggle ${isFav ? "is-fav" : ""}" data-id="${m.id}" title="Agregar a favoritos">
              <i class="bi ${isFav ? "bi-heart-fill" : "bi-heart"}"></i>
            </button>
          </div>
          <div class="mr-card-body">
            <div class="mr-card-title" title="${m.primaryTitle}">${m.primaryTitle}</div>
            <div class="mr-card-meta">
              <span>${m.startYear || "N/D"}</span>
              <span class="mr-rating">${m.averageRating ? m.averageRating.toFixed(1) : "N/D"} <i class="bi bi-star-fill"></i></span>
            </div>
            <div class="mr-stars">${mrStarsHtml(m.averageRating)}</div>
            <a href="reseña.html?id=${encodeURIComponent(m.id)}" class="btn mr-btn-primary text-white w-100">
              <i class="bi bi-eye me-1"></i>Ver reseña
            </a>
          </div>
        </div>
      </div>
    `);
    $grid.append($col);
  });
}
