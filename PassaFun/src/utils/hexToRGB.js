export function hexToRgba(hex, alpha = 0.1) {
  if (hex.charAt(0) === '#') {
    hex = hex.slice(1);
  }

  if (hex.length !== 6) {
    console.error('Invalid hex color format. Must be in #RRGGBB format.');
    return;
  }

  let r = parseInt(hex.slice(0, 2), 16);
  let g = parseInt(hex.slice(2, 4), 16);
  let b = parseInt(hex.slice(4, 6), 16);

  return `rgba(${r}, ${g}, ${b}, ${alpha})`;
}
