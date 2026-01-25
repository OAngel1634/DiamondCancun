document.addEventListener('DOMContentLoaded', function() {
    // Datos de las promociones
    const promotions = [
        {
            tag: "¡Oferta Especial!",
            image: "https://images.unsplash.com/photo-1564604761388-83eafc96f668?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80",
            offer: "Tour en Catamarán",
            occasion: "Para toda la familia",
            discount: "25% de descuento",
            rating: 4.9,
            price: "$1,499",
            originalPrice: "$1,999",
            tax: "Impuestos incluidos"
        },
        {
            tag: "¡Nuevo!",
            image: "https://images.unsplash.com/photo-1590523278191-995cbcda646b?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80",
            offer: "Tour de Snorkel",
            occasion: "Aventura en el Caribe",
            discount: "30% de descuento",
            rating: 4.8,
            price: "$899",
            originalPrice: "$1,299",
            tax: "Impuestos incluidos"
        },
        {
            tag: "¡Promoción!",
            image: "https://images.unsplash.com/photo-1519046904884-53103b34b206?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80",
            offer: "Tour Privado",
            occasion: "Experiencia exclusiva",
            discount: "20% de descuento",
            rating: 5.0,
            price: "$2,999",
            originalPrice: "$3,749",
            tax: "Impuestos incluidos"
        },
        {
            tag: "¡Recomendado!",
            image: "https://images.unsplash.com/photo-1506929562872-bb421503ef21?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80",
            offer: "Tour Romántico",
            occasion: "Para parejas",
            discount: "15% de descuento",
            rating: 4.7,
            price: "$2,499",
            originalPrice: "$2,949",
            tax: "Impuestos incluidos"
        }
    ];

    // Referencias a elementos del DOM
    const carousel = document.getElementById('carousel');
    const indicatorsContainer = document.getElementById('indicators');
    const progressBar = document.getElementById('progressBar');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const promoTemplate = document.getElementById('promo-template');

    let currentIndex = 0;
    let autoSlideInterval;

    // Llenar el carrusel con promociones
    function fillCarousel() {
        carousel.innerHTML = '';
        indicatorsContainer.innerHTML = '';

        promotions.forEach((promo, index) => {
            // Clonar la plantilla
            const promoClone = promoTemplate.content.cloneNode(true);
            const carouselItem = promoClone.querySelector('.carousel-item');

            // Llenar con datos
            carouselItem.querySelector('.promo-tag').textContent = promo.tag;
            const promoImage = carouselItem.querySelector('.promo-image');
            promoImage.src = promo.image;
            promoImage.alt = promo.offer;
            promoImage.onerror = function() {
                this.src = 'https://via.placeholder.com/220?text=Tour+Image';
            };
            carouselItem.querySelector('.offer').textContent = promo.offer;
            carouselItem.querySelector('.occasion').textContent = promo.occasion;
            carouselItem.querySelector('.discount').textContent = promo.discount;
            carouselItem.querySelector('.rating-value').textContent = promo.rating;

            // Crear estrellas
            const starsContainer = carouselItem.querySelector('.stars');
            starsContainer.innerHTML = '';
            for (let i = 0; i < 5; i++) {
                const star = document.createElement('i');
                star.className = i < Math.floor(promo.rating) ? 'fas fa-star' : 'far fa-star';
                starsContainer.appendChild(star);
            }

            carouselItem.querySelector('.price').textContent = promo.price;
            carouselItem.querySelector('.original-price').textContent = promo.originalPrice;
            carouselItem.querySelector('.tax').textContent = promo.tax;

            // Añadir al carrusel
            carousel.appendChild(carouselItem);

            // Crear indicadores
            const indicator = document.createElement('div');
            indicator.classList.add('indicator');
            indicator.dataset.index = index;
            indicator.addEventListener('click', () => {
                goToSlide(index);
                stopAutoSlide();
                startAutoSlide();
            });
            indicatorsContainer.appendChild(indicator);
        });

        // Iniciar
        updateIndicators();
    }

    // Iniciar el carrusel automático
    function startAutoSlide() {
        autoSlideInterval = setInterval(() => {
            nextSlide();
        }, 5000);
    }

    // Detener el carrusel automático
    function stopAutoSlide() {
        clearInterval(autoSlideInterval);
    }

    // Actualizar indicadores
    function updateIndicators() {
        const indicators = document.querySelectorAll('.indicator');
        indicators.forEach((indicator, index) => {
            if (index === currentIndex) {
                indicator.classList.add('active');
            } else {
                indicator.classList.remove('active');
            }
        });

        // Reiniciar la barra de progreso
        progressBar.style.width = '0%';
        void progressBar.offsetWidth; // Reiniciar la animación
        progressBar.style.width = '100%';
    }

    // Cambiar a una diapositiva específica
    function goToSlide(index) {
        if (index < 0) {
            index = promotions.length - 1;
        } else if (index >= promotions.length) {
            index = 0;
        }

        carousel.style.transform = `translateX(-${index * 100}%)`;
        currentIndex = index;
        updateIndicators();
    }

    // Siguiente diapositiva
    function nextSlide() {
        goToSlide(currentIndex + 1);
    }

    // Diapositiva anterior
    function prevSlide() {
        goToSlide(currentIndex - 1);
    }

    // Event listeners para los botones
    prevBtn.addEventListener('click', () => {
        prevSlide();
        stopAutoSlide();
        startAutoSlide();
    });

    nextBtn.addEventListener('click', () => {
        nextSlide();
        stopAutoSlide();
        startAutoSlide();
    });

    // Llenar el carrusel al inicio
    fillCarousel();

    // Iniciar el carrusel automático
    startAutoSlide();

    // Pausar en hover
    document.querySelector('.carousel-container').addEventListener('mouseenter', stopAutoSlide);
    document.querySelector('.carousel-container').addEventListener('mouseleave', startAutoSlide);

    // Botón de reserva
    document.addEventListener('click', function (e) {
        if (e.target.classList.contains('book-btn')) {
            alert(`¡Gracias por tu interés en el tour "${promotions[currentIndex].offer}"! Serás redirigido a la página de reserva.`);
            // Aquí iría la lógica para redirigir a la página de reservas
        }
    });

    // Código para el formulario de reserva
    const tourSelect = document.getElementById('tour');
    const adultosInput = document.getElementById('adultos');
    const ninosInput = document.getElementById('ninos');
    
    // Función para actualizar la imagen en el carrusel
    function updatePromoImage(imageUrl) {
        // Solo actualizar la diapositiva actual del carrusel
        const activeSlide = document.querySelector(`.carousel-item:nth-child(${currentIndex + 1})`);
        if (activeSlide) {
            const img = activeSlide.querySelector('.promo-image');
            if (img) {
                img.src = imageUrl;
                img.onerror = function() {
                    this.src = 'https://via.placeholder.com/220?text=Tour+Image';
                };
            }
        }
    }

    // Actualizar precios
    function updatePrices() {
        const selectedOption = tourSelect.options[tourSelect.selectedIndex];
        const tourPrice = parseFloat(selectedOption.dataset.price);
        const tourImage = selectedOption.dataset.image;
        
        // Actualizar imagen del tour en el carrusel
        if (tourImage) {
            updatePromoImage(tourImage);
        }
        
        const adultos = parseInt(adultosInput.value) || 0;
        const ninos = parseInt(ninosInput.value) || 0;
        
        document.getElementById('adult-count').textContent = adultos;
        document.getElementById('child-count').textContent = ninos;
        
        const precioAdultos = adultos * tourPrice;
        const precioNinos = ninos * (tourPrice * 0.5); // 50% descuento para niños
        const total = precioAdultos + precioNinos;
        
        document.getElementById('adult-price').textContent = '$' + precioAdultos.toFixed(2);
        document.getElementById('child-price').textContent = '$' + precioNinos.toFixed(2);
        document.getElementById('total-price').textContent = '$' + total.toFixed(2);
        
        document.getElementById('precio-total-input').value = total.toFixed(2);
    }
    
    // Event listeners
    tourSelect.addEventListener('change', updatePrices);
    adultosInput.addEventListener('input', updatePrices);
    ninosInput.addEventListener('input', updatePrices);
    
    // Inicializar precios
    updatePrices();
});