import { gsap } from 'gsap';

export function onBeforeEnter(el) {
  el.style.opacity = 0;
  el.style.height = 0;
}

export function onEnter(el, done) {
  gsap.to(el, {
    opacity: 1,
    height: 'auto',
    delay: el.dataset.index * 0.15,
    onComplete: done,
  });
}

export function onLeave(el, done) {
  gsap.to(el, {
    opacity: 0,
    height: 0,
    delay: el.dataset.index * 0.15,
    onComplete: done,
  });
}
