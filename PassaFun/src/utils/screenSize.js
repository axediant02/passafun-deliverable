// utils/screenSize.js

const screenSizeUtils = {
  breakpoints: {
    mobile: 768,
    tablet: 1024,
  },

  isMobile() {
    return window.innerWidth <= this.breakpoints.mobile;
  },

  isTablet() {
    return (
      window.innerWidth > this.breakpoints.mobile && window.innerWidth <= this.breakpoints.tablet
    );
  },

  isDesktop() {
    return window.innerWidth > this.breakpoints.tablet;
  },
};

export default screenSizeUtils;
