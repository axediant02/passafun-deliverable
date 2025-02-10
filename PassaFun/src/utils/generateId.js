export const generateUniqueResultId = () => {
  const randomString = Math.random().toString(36).substring(2, 10);
  return `${randomString}`;
};
