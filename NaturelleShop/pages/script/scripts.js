/*
const thumbnails = document.querySelectorAll('.thumbnail-image-container')
const mainImage = document.querySelector('.product-image-container')

const nextBtn = document.querySelector('.next')
const previousBtn = document.querySelector('.previous')

const lightBoxOverlay = document.querySelector('.lightbox-overlay')
const mainImagesContainer = document.querySelector('.main-images-container')

let lightboxThumbnails;
let lightboxMainImage;

thumbnailClick = (event) => {
  thumbnails.forEach(img => {
    img.classList.remove('active')
  })
  event.target.parentElement.classList.add('active')
  mainImage.src = event.target.src.replace('-thumbnail', '')
}

nextBtnClick = () => {
  let imageIndex = getCurrentImageIndex()
  imageIndex++
  if (imageIndex > 4) {
    imageIndex = 1
  }
  setMainImage(imageIndex)
}

previousBtnClick = () => {
  let imageIndex = getCurrentImageIndex()
  imageIndex--
  if (imageIndex < 1) {
    imageIndex = 4
  }
  setMainImage(imageIndex)
}

getCurrentImageIndex = () => {
  const imageIndex = parseInt(mainImage.src.split('\\').pop().split('/').pop().replace('.jpg', '').replace('image-product-', ''))
  return imageIndex
}

setMainImage = (imageIndex) => {
  mainImage.src = `images/image-product-${imageIndex}.jpg`
  thumbnails.forEach(img => {
    img.classList.remove('active')
  })
  thumbnails[imageIndex - 1].classList.add('active')
}

MainImageClick = () => {
  if (window.innerWidth >= 1400) {
    if (lightBoxOverlay.childElementCount == 1) {
      const newLightbox = mainImagesContainer.cloneNode(true)
      lightBoxOverlay.appendChild(newLightbox)

      const overlayCloseBtn = document.querySelector('#overlayClose')
      overlayCloseBtn.addEventListener('click', closeLightbox)

      lightboxThumbnails = lightBoxOverlay.querySelectorAll('.thumbnail-image-container')
      lightboxMainImage = lightBoxOverlay.querySelector('.product-image')

      lightboxThumbnails.forEach(img => {
        img.addEventListener('click', lightboxThumbnailClick)
      })

      const lightboxNextBtn = lightBoxOverlay.querySelector('.next')
      const lightboxPreviousBtn = lightBoxOverlay.querySelector('.previous')

      lightboxNextBtn.addEventListener('click', lightboxNextBtnClick)
      lightboxPreviousBtn.addEventListener('click', lightboxPreviousBtnClick)
    }

    lightBoxOverlay.classList.remove('hidden')
  }
}





closeLightbox = () => {
  lightBoxOverlay.classList.add('hidden')
}

lightboxThumbnailClick = (event) => {
  lightboxThumbnails.forEach(img => {
    img.classList.remove('active')
  })
  event.target.parentElement.classList.add('active')
  lightboxMainImage.src = event.target.src.replace('-thumbnail', '')
}

lightboxNextBtnClick = () => {
  let imageIndex = lightboxGetCurrentImageIndex()
  imageIndex++
  if (imageIndex > 4) {
    imageIndex = 1
  }
  lightboxSetMainImage(imageIndex)
}

lightboxPreviousBtnClick = () => {
  let imageIndex = lightboxGetCurrentImageIndex()
  imageIndex--
  if (imageIndex < 1) {
    imageIndex = 4
  }
  lightboxSetMainImage(imageIndex)
}

lightboxGetCurrentImageIndex = () => {
  const imageIndex = parseInt(lightboxMainImage.src.split('\\').pop().split('/').pop().replace('.jpg', '').replace('image-product-', ''))
  return imageIndex
}

lightboxSetMainImage = (imageIndex) => {
  lightboxMainImage.src = `images/image-product-${imageIndex}.jpg`
  lightboxThumbnails.forEach(img => {
    img.classList.remove('active')
  })
  lightboxThumbnails[imageIndex-1].classList.add('active')
}


thumbnails.forEach(img => {
  img.addEventListener('click', thumbnailClick)
})

nextBtn.addEventListener('click', nextBtnClick)
previousBtn.addEventListener('click', previousBtnClick)

mainImage.addEventListener('click', MainImageClick)*/


// Select all the thumbnail containers and main image container
const thumbnails = document.querySelectorAll('.thumbnail-image-container img');
const mainImage = document.querySelector('.product-image-container img');

// Select next and previous buttons
const nextBtn = document.querySelector('.next');
const previousBtn = document.querySelector('.previous');

// Lightbox related elements (if any)
const lightBoxOverlay = document.querySelector('.lightbox-overlay');
const mainImagesContainer = document.querySelector('.main-images-container');

let currentIndex = 0;
let totalThumbnails = thumbnails.length; // Get the total number of thumbnails

// Handle thumbnail click
thumbnails.forEach((thumbnail, index) => {
  thumbnail.addEventListener('click', (event) => {
    currentIndex = index; // Update the current index based on the clicked thumbnail
    updateMainImage(thumbnail.src); // Update main image when clicking on a thumbnail
    highlightActiveThumbnail(thumbnail);
  });
});

// Function to update the main image
function updateMainImage(src) {
  mainImage.src = src.replace('-thumbnail', ''); // Adjust the image source
}

// Highlight active thumbnail
function highlightActiveThumbnail(selectedThumbnail) {
  thumbnails.forEach(thumb => {
    thumb.parentElement.classList.remove('active');
  });
  selectedThumbnail.parentElement.classList.add('active');
}

// Handle Next Button Click
nextBtn.addEventListener('click', () => {
  currentIndex++;
  if (currentIndex >= totalThumbnails) {
    currentIndex = 0; // Loop back to the first thumbnail
  }
  updateMainImage(thumbnails[currentIndex].src);
  highlightActiveThumbnail(thumbnails[currentIndex]);
});

// Handle Previous Button Click
previousBtn.addEventListener('click', () => {
  currentIndex--;
  if (currentIndex < 0) {
    currentIndex = totalThumbnails - 1; // Loop back to the last thumbnail
  }
  updateMainImage(thumbnails[currentIndex].src);
  highlightActiveThumbnail(thumbnails[currentIndex]);
});

// Lightbox logic (if applicable)
mainImage.addEventListener('click', () => {
  if (window.innerWidth >= 1400) {
    if (lightBoxOverlay.childElementCount == 1) {
      const newLightbox = mainImagesContainer.cloneNode(true);
      lightBoxOverlay.appendChild(newLightbox);
      const overlayCloseBtn = document.querySelector('#overlayClose');
      overlayCloseBtn.addEventListener('click', closeLightbox);

      lightboxThumbnails = lightBoxOverlay.querySelectorAll('.thumbnail-image-container img');
      lightboxMainImage = lightBoxOverlay.querySelector('.product-image');

      lightboxThumbnails.forEach((img, index) => {
        img.addEventListener('click', () => {
          updateMainImage(img.src);
          highlightActiveThumbnail(img);
        });
      });

      const lightboxNextBtn = lightBoxOverlay.querySelector('.next');
      const lightboxPreviousBtn = lightBoxOverlay.querySelector('.previous');

      lightboxNextBtn.addEventListener('click', nextBtnClick);
      lightboxPreviousBtn.addEventListener('click', previousBtnClick);
    }

    lightBoxOverlay.classList.remove('hidden');
  }
});

function closeLightbox() {
  lightBoxOverlay.classList.add('hidden');
}
