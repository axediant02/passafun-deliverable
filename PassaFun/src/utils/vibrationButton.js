export function vibrate(duration = 50) {
  if ('vibrate' in navigator) {
    navigator.vibrate(duration);
  } else {
    console.warn('Vibration API not supported on this device.');
  }
}
