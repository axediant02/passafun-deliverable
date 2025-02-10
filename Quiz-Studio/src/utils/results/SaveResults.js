import axios from "axios";
import { getAuth } from "@/pages/auth/authService";

export const submitQuizResult = async ({
  quizId,
  result,
  imageFile,
  imageRemoved,
  previewImage,
  onSuccess,
  onError,
  onComplete
}) => {
  const formData = new FormData();
  
  formData.append("header", result.header || "");
  formData.append("description", result.description || "");
  formData.append("financial_tips", "");
  
  if (imageFile) {
    formData.append("image", imageFile);
  }
  if (imageRemoved) {
    formData.append("image_removed", "1");
  }
  
  formData.append(
    "min_points",
    result.min_points !== null && result.min_points !== undefined
      ? result.min_points
      : ""
  );
  formData.append(
    "max_points",
    result.max_points !== null && result.max_points !== undefined
      ? result.max_points
      : ""
  );

  try {
    const response = await axios.post(
      `/api/quizzes/${quizId}/results`,
      formData,
      {
        headers: {
          "Content-Type": "multipart/form-data",
          Authorization: `Bearer ${getAuth().token}`,
        },
      }
    );

    const newResult = {
      id: response.data.result.id,
      quiz_id: quizId,
      header: result.header,
      description: result.description,
      image: previewImage,
      image_url: response.data.result.image_url,
      min_points: result.min_points,
      max_points: result.max_points,
    };

    onSuccess?.(newResult);
    window.$snackbar("Quiz Result created successfully!", "success");

  } catch (error) {
    if (error.response?.status === 403) {
      window.$snackbar(
        "Oops! You don't have access to perform this action!",
        "error"
      );
    } else if (error.response?.status === 422) {
      const errorMessage =
        error.response.data.message ||
        error.response.data.error ||
        "An error occurred";
      window.$snackbar(errorMessage, "error");
    } else {
      window.$snackbar("Error! Please fill in all required fields", "error");
    }
    onError?.(error);
  } finally {
    onComplete?.();
  }
};




export const editQuizResult = async ({
  editingIndex,
  result,
  imageFile,
  imageRemoved,
  previewImage,
  savedResults,
  onSuccess,
  onError,
  onComplete
}) => {
  if (editingIndex === null) return;

  try {
    const formData = new FormData();
    formData.append("header", result.header);
    formData.append("description", result.description);

    const savedResult = savedResults[editingIndex];
    const isDefaultResult = savedResult.min_points === 0;

    if (imageFile) {
      formData.append("image", imageFile);
    }
    if (imageRemoved) {
      formData.append("image_removed", "1");
    }
    
    if (isDefaultResult) {
      formData.append("min_points", 0);
    } else {
      formData.append("min_points", result.min_points);
    }
    formData.append("max_points", result.max_points);

    const resultId = savedResults[editingIndex].id;
    const { token } = getAuth();
    
    const response = await axios.post(`/api/results/${resultId}`, formData, {
      headers: {
        Authorization: `Bearer ${token}`,
        "Content-Type": "multipart/form-data",
        Accept: "application/json",
      },
    });

    window.$snackbar("Quiz Result updated successfully!", "success");
    const updatedResult = {
      id: resultId,
      header: result.header,
      description: result.description,
      image: previewImage,
      image_url: response.data.result.image_url,
      min_points: response.data.result.min_points,
      max_points: response.data.result.max_points,
    };

    if (onSuccess) {
      onSuccess(updatedResult, editingIndex);
    }

  } catch (error) {
    if (error.response?.status === 403) {
      window.$snackbar(
        "Oops! You don't have access to perform this action!",
        "error"
      );
    } else if (error.response?.status === 422) {
      const errorMessage =
        error.response.data.message ||
        error.response.data.error ||
        "An error occurred";
      window.$snackbar(errorMessage, "error");
    }

    if (onError) {
      onError(error);
    }
  } finally {
    if (onComplete) {
      onComplete();
    }
  }
};



const changeQuizStatus = async (quizId) => {
  const { token } = getAuth();
  try {
    await axios.patch(
      `/api/quizzes/${quizId}`,
      { quiz_status_id: 2 },
      {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      }
    );
    window.$snackbar("Quiz published successfully!", "success");
  } catch (error) {
    if (error.response?.status === 403) {
      window.$snackbar(
        "You don't have permission to perform this action.",
        "error"
      );
    }
    throw error;
  }
};

const handleError = (error, onError) => {
  if (error.response?.data) {
    const errors = error.response.data.errors || [error.response.data.message];
    const message = error.response.data.message || "Failed to publish quiz";
    onError({ errors, message });
  } else {
    onError({ message: "Failed to publish quiz" });
  }
  console.error("Error publishing quiz:", error);
};

export const publishQuiz = async ({
  quizId,
  isFormEmpty,
  submitForm,
  onSuccess,
  onError,
}) => {
  if (isFormEmpty) {
    try {
      await changeQuizStatus(quizId);
      onSuccess();
    } catch (error) {
      handleError(error, onError);
    }
    return;
  }

  try {
    await submitForm("published");
    await changeQuizStatus(quizId);
    onSuccess();
  } catch (error) {
    handleError(error, onError);
  }
};