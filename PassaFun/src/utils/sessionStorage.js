export const removeQuizInSessionStorage = (quiz, getResult, userResponses, quizStatus) => {
  sessionStorage.removeItem(quiz);
  sessionStorage.removeItem(getResult);
  sessionStorage.removeItem(userResponses);
  sessionStorage.removeItem(quizStatus);
};
