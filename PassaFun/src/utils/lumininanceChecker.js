function hexToRgb(hex) {
  if (!hex || hex.charAt(0) !== '#') {
    return { r: 0, g: 0, b: 0 };
  }

  hex = hex.slice(1);

  let r = parseInt(hex.slice(0, 2), 16);
  let g = parseInt(hex.slice(2, 4), 16);
  let b = parseInt(hex.slice(4, 6), 16);

  return { r, g, b };
}

function calculateLuminance({ r, g, b }) {
  const a = [r, g, b].map(function (v) {
    v /= 255;
    return v <= 0.03928 ? v / 12.92 : Math.pow((v + 0.055) / 1.055, 2.4);
  });

  return a[0] * 0.2126 + a[1] * 0.7152 + a[2] * 0.0722;
}

export function isDarkColor(hex) {
  if (!hex || hex.charAt(0) !== '#') {
    return false;
  }

  const { r, g, b } = hexToRgb(hex);
  const luminance = calculateLuminance({ r, g, b });
  return luminance < 0.5;
}

export function shouldUseLightText(theme) {
  if (theme.background_type === 'color' && theme.background_value && typeof theme.background_value === 'string') {
    return isDarkColor(theme.background_value);
  }
  return false;
}
