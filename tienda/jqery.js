$(document).ready(function () {

    console.log('Landing page de tienda cargada (jQuery)');

    // Instancias de los modales Bootstrap
    const cartModal = new bootstrap.Modal(document.getElementById('carritoModal'));
    const subModal = new bootstrap.Modal(document.getElementById('suscripcionModal'));
    const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));

    // CARRITO DE COMPRAS =====
    let carrito = {}; // { "Nombre producto": { precio: 39.99, cantidad: 2 }, ... }

    function renderizarCarrito() {
        const $items = $('#carritoItems');
        const nombres = Object.keys(carrito);

        $items.empty();

        if (nombres.length === 0) {
            $items.html('<p class="text-muted text-center mb-0" id="carritoVacioMsg">Tu carrito está vacío.</p>');
        } else {
            nombres.forEach(function (nombre) {
                const item = carrito[nombre];
                const subtotal = (item.precio * item.cantidad).toFixed(2);

                const $fila = $(
                    '<div class="d-flex justify-content-between align-items-center mb-2 carrito-item" data-product="' + nombre + '">' +
                        '<div class="me-2">' +
                            '<div class="fw-semibold small">' + nombre + '</div>' +
                            '<div class="text-muted small">$' + item.precio.toFixed(2) + ' c/u · $' + subtotal + '</div>' +
                        '</div>' +
                        '<div class="d-flex align-items-center">' +
                            '<button type="button" class="btn btn-sm btn-outline-secondary btn-restar-cantidad">−</button>' +
                            '<span class="mx-2 cantidad-item">' + item.cantidad + '</span>' +
                            '<button type="button" class="btn btn-sm btn-outline-secondary btn-sumar-cantidad">+</button>' +
                        '</div>' +
                    '</div>'
                );
                $items.append($fila);
            });
        }

        // Total y contador
        let total = 0;
        let totalUnidades = 0;
        nombres.forEach(function (nombre) {
            total += carrito[nombre].precio * carrito[nombre].cantidad;
            totalUnidades += carrito[nombre].cantidad;
        });

        $('#carritoTotal').text('$' + total.toFixed(2));

        if (totalUnidades > 0) {
            $('#carritoContador').text(totalUnidades).removeClass('d-none');
        } else {
            $('#carritoContador').addClass('d-none');
        }
    }

    // BOTONES DE AÑADIR AL CARRITO =====
    $('.btn-add-to-cart').on('click', function (e) {
        e.preventDefault();

        // Obtener datos del producto
        const product = $(this).data('product');
        const price = parseFloat($(this).data('price'));

        // Agregar o incrementar en el carrito
        if (carrito[product]) {
            carrito[product].cantidad += 1;
        } else {
            carrito[product] = { precio: price, cantidad: 1 };
        }
        renderizarCarrito();

        // Actualizar modal de confirmación
        $('#modalProductName').text(product);
        $('#modalProductPrice').text('$' + price.toFixed(2));

        // Mostrar modal
        cartModal.show();
    });

    // AUMENTAR / DISMINUIR CANTIDAD DENTRO DEL DROPDOWN (elementos dinámicos) =====
    $('#carritoItems').on('click', '.btn-sumar-cantidad', function () {
        const nombre = $(this).closest('.carrito-item').data('product');
        carrito[nombre].cantidad += 1;
        renderizarCarrito();
    });

    $('#carritoItems').on('click', '.btn-restar-cantidad', function () {
        const nombre = $(this).closest('.carrito-item').data('product');
        carrito[nombre].cantidad -= 1;

        if (carrito[nombre].cantidad <= 0) {
            delete carrito[nombre];
        }
        renderizarCarrito();
    });

    // VACIAR CARRITO =====
    $('#btnVaciarCarrito').on('click', function () {
        carrito = {};
        renderizarCarrito();
    });

    // BOTÓN DE SUSCRIPCIÓN =====
    $('#btnSubscribe').on('click', function () {
        const email = $('#emailInput').val().trim();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (email && emailRegex.test(email)) {
            $('#modalEmailSubscription').text(email);
            subModal.show();
            $('#emailInput').val('');
        } else {
            errorModal.show();
        }
    });

    // SCROLL SUAVE PARA ENLACES DEL NAVBAR =====
    $('a[href^="#"]').on('click', function (e) {
        const target = $($(this).attr('href'));

        if (target.length) {
            e.preventDefault();
            $('html, body').animate({
                scrollTop: target.offset().top - 70
            }, 600);
        }
    });

    // EVENTOS DE CIERRE DE MODALES =====
    $('#carritoModal').on('hidden.bs.modal', function () {
        console.log('Modal de carrito cerrado (jQuery)');
    });

    $('#suscripcionModal').on('hidden.bs.modal', function () {
        console.log('Modal de suscripción cerrado (jQuery)');
    });

    console.log('Todos los modales están listos (jQuery)');

    // BÚSQUEDA Y FILTRO POR CATEGORÍA =====
    let categoriaActual = 'todos';

    function aplicarFiltros() {
        const texto = $('#buscarProducto').val().trim().toLowerCase();
        let visibles = 0;

        $('.producto-item').each(function () {
            const $item = $(this);
            const nombre = $item.data('nombre');
            const categoria = $item.data('categoria');

            const coincideTexto = nombre.indexOf(texto) !== -1;
            const coincideCategoria = categoriaActual === 'todos' || categoria === categoriaActual;

            if (coincideTexto && coincideCategoria) {
                $item.show();
                visibles++;
            } else {
                $item.hide();
            }
        });

        $('#sinResultados').toggleClass('d-none', visibles !== 0);
    }

    // Buscar mientras se escribe
    $('#buscarProducto').on('keyup', aplicarFiltros);

    // Botones de categoría
    $('.btn-filtro').on('click', function () {
        $('.btn-filtro').removeClass('active');
        $(this).addClass('active');

        categoriaActual = $(this).data('categoria');
        aplicarFiltros();
    });

    // BOTÓN "LIMPIAR BÚSQUEDA" (aparece cuando no hay resultados) =====
    $('#btnLimpiarBusqueda').on('click', function () {
        $('#buscarProducto').val('');

        $('.btn-filtro').removeClass('active');
        $('.btn-filtro[data-categoria="todos"]').addClass('active');
        categoriaActual = 'todos';

        aplicarFiltros();
    });
});