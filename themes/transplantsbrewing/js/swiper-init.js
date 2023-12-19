document.addEventListener('DOMContentLoaded', function() {
    var headerSwiper = new Swiper('.fixed-carousel .swiper-container', {
        autoplay: {
            delay: 50,
            disableOnInteraction: false,
        },
        loop: true,
        fadeEffect: {
            crossFade: true
        },
        effect: 'fade',
        speed: 5000
    });

    // Function to toggle body scroll
    function toggleBodyScroll(disable) {
        document.body.style.overflow = disable ? 'hidden' : '';
    }

    // Variables and setup for beverage modal swiper
    let beverageModalSwiper;
    let beverageSlides = [];
    
    document.querySelectorAll('.beverage-frame').forEach(function(frame) {
        let imageSrc = frame.querySelector('.beverage-image').getAttribute('src');
        let title = frame.dataset.title;
        let description = frame.dataset.description;
        let abv = frame.dataset.abv;

        let slideContent = `
            <div class="swiper-slide">
                <div class="beverage-post card beverage-card">
                    <img class="card-img-top" src="${imageSrc}" alt="${title}" loading="lazy">
                    <div class="card-body">
                        <h1 class="card-title">${title}</h1>
                        <h2 class="card-text">${description}</h2>
                        <h5 class="card-text">${abv}</h5>
                    </div>
                </div>
            </div>
        `;
        beverageSlides.push(slideContent);
    });

    document.querySelector('.beverage-modal .swiper-wrapper').innerHTML = beverageSlides.join('');

    beverageModalSwiper = new Swiper('.beverage-modal .swiper-container', {
        loop: false, 
        autoplay: false,
        spaceBetween: 10,
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
    });

    document.querySelectorAll('.beverage-frame').forEach(function(frame, index) {
        frame.addEventListener('click', function() {
            beverageModalSwiper.slideTo(index);
            document.querySelector('.beverage-modal').style.display = 'block';
            toggleBodyScroll(true);
        });
    });

    document.querySelector('.beverage-modal-close').addEventListener('click', function(e) {
        beverageModalSwiper.slideTo(0, 0);
        document.querySelector('.beverage-modal').style.display = 'none';
        toggleBodyScroll(false);
    });

    const eventModal = document.querySelector('.event-modal');
    const swiperWrapper = eventModal.querySelector('.swiper-wrapper');
    const iframeContainer = document.createElement('div');
    iframeContainer.classList.add('event-iframe-container');
    eventModal.appendChild(iframeContainer);

    const closeButton = document.querySelector('.event-modal-close');
    closeButton.addEventListener('click', function() {
        eventModal.style.display = 'none';
        eventModalSwiper.slideTo(0, 0);
        toggleBodyScroll(false); // Enable scrolling
    });

    let eventSlides = [];
    let originalOrderEvents = Array.from(document.querySelectorAll('#all-events .event-post'));

    document.querySelectorAll('.event-post').forEach(function(eventElem) {
        const imageElem = eventElem.querySelector('.card-img-top');
        const imageSrc = imageElem ? imageElem.getAttribute('src') : 'default-image.jpg';
        const performersJSON = eventElem.dataset.performers || '[]';
        const performers = JSON.parse(performersJSON);
        const eventLink = eventElem.dataset.eventLink || '#';

        const performersContent = performers.map(performer => `<div class="performer-name">${performer.name}</div>`).join('');
        const eventTitle = eventElem.dataset.title || ''; 
        const titleContent = eventTitle ? `<h1 id="event-title">${eventTitle}</h1>` : '';
        
        const slideContent = `
        <div class="swiper-slide">
            <div class="event-post card event-card">
                <img class="card-img-top" src="${imageSrc}" alt="Event image" loading="lazy">
                <div class="card-body">
                <div class="bg-card-overlay"></div>
                    <img class="modal-bg-image" src="${imageSrc}" alt="Event image" loading="lazy">
                    <div class="card-overlay"></div>
                    <a href="${eventLink}" target="_blank" class="btn btn-primary event-link swiper-no-swiping" data-iframe-src="${eventLink}">BUY TICKETS</a>
                    ${titleContent} 
                    <div class="performers">${performersContent}</div>
                    <p class="card-text" id="description">${eventElem.dataset.description || 'No description available'}</p>
                    <p class="card-text">Price: ${eventElem.dataset.price || 'Free'}</p>
                    <p class="card-text">Age: ${eventElem.dataset.age || 'All Ages'}</p>
                </div>
            </div>
        </div>
    `;
        eventSlides.push(slideContent);
    });

    swiperWrapper.innerHTML = eventSlides.join('');

    const eventModalSwiper = new Swiper('.event-modal .swiper-container', {
        loop: false,
        autoplay: false,
        spaceBetween: 10,
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
        watchOverflow: true,
        slideVisibleClass: 'swiper-slide-visible',
        slideActiveClass: 'swiper-slide-active',
        slideNextClass: 'swiper-slide-next',
        slidePrevClass: 'swiper-slide-prev',
        noSwipingSelector: 'a',
        noSwipingClass: 'swiper-no-swiping',
        preventClicks: false,
        preventClicksPropagation: false,
    });

    eventModalSwiper.on('slideChange', function() {
        console.log("Slide changed to index:", eventModalSwiper.realIndex);
    });

    document.querySelectorAll('.event-post').forEach(function(eventElem) {
        eventElem.addEventListener('click', function(e) {
            e.preventDefault();
            const clickedEventId = eventElem.dataset.eventId;
            const correspondingIndex = originalOrderEvents.findIndex(el => el.dataset.eventId === clickedEventId);
            if (correspondingIndex !== -1) {
                console.log("Event card clicked, going to slide index:", correspondingIndex);
                eventModalSwiper.slideTo(correspondingIndex);
                eventModal.style.display = 'block';
                toggleBodyScroll(true);
            } else {
                console.error('Matching event not found in original order');
            }
        });
    });

    document.querySelector('.swiper-button-next').addEventListener('click', function() {
        console.log("Next button clicked");
    });

    document.querySelector('.swiper-button-prev').addEventListener('click', function() {
        console.log("Previous button clicked");
    });

    function openInIframe(iframeSrc) {
        iframeContainer.innerHTML = '';
        const iframe = document.createElement('iframe');
        iframe.style.display = 'none';
        iframe.src = iframeSrc;
        iframe.style.width = '100%';
        iframe.style.height = '100vh';
        iframe.style.border = 'none';
        iframe.onload = function() {
            iframe.style.display = 'block';
            document.body.style.overflow = '';
        };
        document.body.style.overflow = 'hidden';
        console.log("Opening iframe, hiding main scrollbar");

        const backButton = document.createElement('button');
        backButton.textContent = 'Back';
        backButton.style.position = 'absolute';
        backButton.style.top = '10px';
        backButton.style.left = '10px';
        backButton.style.zIndex = '1000';
        backButton.onclick = function() {
            iframeContainer.style.display = 'none';
            iframeContainer.removeChild(iframe);
            document.body.style.overflow = '';
        };

        iframeContainer.appendChild(backButton);
        iframeContainer.appendChild(iframe);
        iframeContainer.style.display = 'block';
        iframeContainer.style.position = 'fixed';
        iframeContainer.style.top = '0';
        iframeContainer.style.left = '0';
        iframeContainer.style.width = '100%';
        iframeContainer.style.height = '100%';
        iframeContainer.style.backgroundColor = 'white';
        iframeContainer.style.zIndex = '999';
    }

    var buyTicketsButtons = swiperWrapper.querySelectorAll('.event-link');
    buyTicketsButtons.forEach(function(button) {
        button.addEventListener('click', function(event) {
            event.preventDefault();
            event.stopPropagation();
            const iframeSrc = button.dataset.iframeSrc;
            openInIframe(iframeSrc);
        });
       
        const featuredEventInfoButtons = document.querySelectorAll('.featured-event-notification .more-info');
        featuredEventInfoButtons.forEach(button => {
            button.addEventListener('click', function(event) {
                const eventPost = event.target.closest('.event-post, .featured-event-post');
                if (eventPost) {
                    const eventId = eventPost.dataset.eventId;
                    const correspondingIndex = originalOrderEvents.findIndex(el => el.dataset.eventId === eventId);
                    if (correspondingIndex !== -1) {
                        eventModalSwiper.slideTo(correspondingIndex);
                        eventModal.style.display = 'block';
                        toggleBodyScroll(true);
                    }
                }
            });
        });
    });
});


    

