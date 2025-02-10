export const validateEmail = (email) => {
  const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
  return emailRegex.test(email) ? null : 'Please enter a valid email';
};

export const validatePhone = (phone) => {
  const phoneRegex = /^\+?(?:\d{1,3})?[-\s]?(?:\(?\d{1,4}\)?[-\s]?)?\d{5,12}$/;
  return phoneRegex.test(phone) ? null : 'Please enter a valid phone number';
};

export const validateText = (text) => {
  const trimmedText = text.trim();
  const restrictedCharsRegex = /[!@#$%^&*()_+={}\[\]|<>~`]+/;
  
  if (trimmedText.length < 3) {
    return 'Please enter your full name';
  }
  if (trimmedText.length > 120) {
    return 'Text cannot exceed 120 characters';
  }
  if (restrictedCharsRegex.test(trimmedText)) {
    return 'Some special characters are not allowed';
  }
  return null;
};

export const validateAge = (age) => {
  if (!/^\d+$/.test(age)) {
    return 'Please enter a valid age';
  }
  const parsedAge = parseInt(age);
  return (!isNaN(parsedAge) && parsedAge >= 0 && parsedAge <= 120)
    ? null
    : 'Please enter a valid age';
};
