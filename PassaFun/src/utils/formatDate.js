export const formatDateLong = (date) => {
  const parsedDate = new Date(date);

  if (parsedDate.toString() === 'Invalid Date') {
    throw new Error('Invalid date provided.');
  }

  return parsedDate.toLocaleDateString(undefined, {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  });
};

export const formatDateShort = (date) => {
  const parsedDate = new Date(date);

  if (parsedDate.toString() === 'Invalid Date') {
    throw new Error('Invalid date provided.');
  }

  return parsedDate.toLocaleDateString(undefined, {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  });
};
