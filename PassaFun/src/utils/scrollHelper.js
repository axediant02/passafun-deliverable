const scrollHelper = (() => {
  let lastScrollY = 0;

  const onScroll = (onScrollDown, onScrollUp) => {
    window.addEventListener('scroll', () => {
      const currentScrollY = window.scrollY;

      if (currentScrollY > lastScrollY && typeof onScrollDown === 'function') {
        onScrollDown();
      } else if (currentScrollY < lastScrollY && typeof onScrollUp === 'function') {
        onScrollUp();
      }

      lastScrollY = currentScrollY;
    });
  };

  return {
    onScroll,
  };
})();

export default scrollHelper;
