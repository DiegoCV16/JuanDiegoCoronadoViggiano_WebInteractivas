/* =========================================================
   MovieReviews - reseña.html
   ========================================================= */

let mrCurrentMovie = null;

$(function () {
  const params = new URLSearchParams(window.location.search);
  const id = params.get("id");

  if (!id) {
    mrShowDetailError("No se especificó ninguna película (falta el parámetro ?id=).");
    return;
  }

  mrLoadMovieById(
    id,
    function (movie) {
      mrCurrentMovie = movie;
      mrSetMoviesCache([...MR_ALL_MOVIES_CACHE.filter((m) => m.id !== movie.id), movie]);
      mrRenderDetail(movie);
    },
    function () {
      mrShowDetailError("No se encontró información para esta película.");
    }
  );

  // Botón de favorito en el detalle
  $("#mr-d-fav-btn").on("click", function () {
    if (!mrCurrentMovie) return;
    const isFav = MRFavorites.toggle(mrCurrentMovie.id);
    mrUpdateFavButton(isFav);
  });
});

function mrShowDetailError(message) {
  $("#mr-loading").addClass("d-none");
  $("#mr-detail-wrap").addClass("d-none");
  $("#mr-error-msg").text(message);
  $("#mr-error").removeClass("d-none");
}

function mrUpdateFavButton(isFav) {
  const $btn = $("#mr-d-fav-btn");
  $btn.toggleClass("btn-danger", isFav).toggleClass("btn-outline-danger", !isFav);
  $btn.find("i").toggleClass("bi-heart-fill", isFav).toggleClass("bi-heart", !isFav);
  $("#mr-d-fav-label").text(isFav ? "En favoritos" : "Favorito");
}

function mrRenderDetail(m) {
  $("#mr-loading").addClass("d-none");

  document.title = `MovieReviews - ${m.primaryTitle}`;

  $("#mr-d-poster").attr("src", m.primaryImage || "https://placehold.co/400x600?text=Sin+imagen").attr("alt", m.primaryTitle);
  $("#mr-d-title").text(m.primaryTitle);
  $("#mr-d-year").text(m.startYear || "N/D");
  $("#mr-d-runtime").text(m.runtimeMinutes || "N/D");
  $("#mr-d-content-rating").text(m.contentRating || "N/D");

  const $genres = $("#mr-d-genres").empty();
  m.genres.forEach((g) => $genres.append(`<span class="mr-badge">${g}</span>`));

  $("#mr-d-rating").text(m.averageRating ? m.averageRating.toFixed(1) : "N/D");
  $("#mr-d-stars").html(mrStarsHtml(m.averageRating));
  $("#mr-d-votes").text(m.numVotes ? `(${m.numVotes.toLocaleString("es-MX")} votos)` : "");

  if (m.metascore !== null && m.metascore !== undefined) {
    $("#mr-d-metascore-wrap").removeClass("d-none");
    $("#mr-d-metascore-value").text(m.metascore);
    const $fill = $("#mr-d-metascore-fill");
    $fill.css("width", m.metascore + "%");
    $fill.removeClass("mr-score-good mr-score-mid mr-score-low");
    if (m.metascore >= 70) $fill.addClass("mr-score-good");
    else if (m.metascore >= 50) $fill.addClass("mr-score-mid");
    else $fill.addClass("mr-score-low");
  } else {
    $("#mr-d-metascore-wrap").addClass("d-none");
  }

  $("#mr-d-description").text(m.description);

  const $interests = $("#mr-d-interests").empty();
  if (m.interests.length) {
    m.interests.forEach((i) => $interests.append(`<span class="mr-badge">${i}</span>`));
  } else {
    $interests.text("N/D");
  }

  $("#mr-d-languages").text(m.spokenLanguages.length ? m.spokenLanguages.join(", ").toUpperCase() : "N/D");
  $("#mr-d-countries").text(m.countriesOfOrigin.length ? m.countriesOfOrigin.join(", ") : "N/D");
  $("#mr-d-budget").text(mrFormatMoney(m.budget));
  $("#mr-d-gross").text(mrFormatMoney(m.grossWorldwide));
  $("#mr-d-companies").text(m.productionCompanies.length ? m.productionCompanies.join(", ") : "N/D");

  if (m.trailer) {
    $("#mr-d-trailer-btn").attr("href", m.trailer).removeClass("disabled");
  } else {
    $("#mr-d-trailer-btn").addClass("disabled").attr("href", "#");
  }

  const $links = $("#mr-d-links").empty();
  if (m.externalLinks.length) {
    m.externalLinks.forEach((link) => {
      let label = link;
      try {
        label = new URL(link).hostname.replace("www.", "");
      } catch (e) {}
      $links.append(`<a href="${link}" target="_blank" class="mr-external-link">${label}</a>`);
    });
  } else {
    $("#mr-d-links-wrap").addClass("d-none");
  }

  mrUpdateFavButton(MRFavorites.isFavorite(m.id));

  $("#mr-detail-wrap").removeClass("d-none").hide().fadeIn(400);
}
