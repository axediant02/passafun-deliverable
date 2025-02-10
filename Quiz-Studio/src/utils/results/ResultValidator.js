export const validateBasicFields = (result, showSnackbar, errorMessage) => {
  if (!result.header.trim()) {
    showSnackbar("Result name is required", "error");
    // errorMessage.header = "Result name is required";
    return false;
  } else {
    errorMessage.header = "";
  }

  if (!result.description.trim()) {
    showSnackbar("Result description is required", "error");
    // errorMessage.description = "Result description is required";
    return false;
  } else {
    errorMessage.description = "";
  }

  return true;
};


export const validatePoints = ({ min_points, max_points }, showError, errorMessage) => {
  const isDefault = min_points === 0;
  let isValid = true;
  if (!isDefault && isEmptyValue(min_points)) {
    // errorMessage.min_points = "Minimum score is required";
    showError("Minimum score is required", "error");
    isValid = false;
  } else {
    // errorMessage.min_points = "";
  }

  if (isEmptyValue(max_points)) {
    // errorMessage.max_points = "Maximum score is required";
    showError("Maximum score is required", "error");
    isValid = false;
  } else {
    errorMessage.max_points = "";
  }

  if (isEmptyValue(min_points) && isEmptyValue(max_points)) {
    // errorMessage.min_points = "Minimum score is required";
    // errorMessage.max_points = "Maximum score is required";
    showError("Minimum and Maximum score are required", "error");
    isValid = false;
  } else {
    // errorMessage.min_points = "";
    // errorMessage.max_points = "";
  }

  return isValid;
};

const isEmptyValue = (value) => {
  return value === null || value === undefined || value === "";
};

export const validatePointRanges = (result, perfectScore, showSnackbar, errorMessage) => {
  const minPointsNum = Number(result.min_points);
  const maxPointsNum = Number(result.max_points);

  if (minPointsNum > maxPointsNum) {
    // errorMessage.min_points = "Exceeds Maximum score";
    showSnackbar("Minimum score cannot be greater than maximum score", "error");
    return false;
  } else {
    // errorMessage.min_points = "";
  }

  if (maxPointsNum > perfectScore) {
    // errorMessage.max_points = "Exceeds the perfect score";
    showSnackbar(
      `Maximum score cannot exceed the perfect score (${perfectScore})`,
      "error"
    );
    return false;
  } else {
    // errorMessage.max_points = "";
  }

  return true;
};


export const validateOverlappingRanges = (result, savedResults, editingIndex, showError, errorMessage) => {
  const minPointsNum = Number(result.min_points);
  const maxPointsNum = Number(result.max_points);

  const overlappingResult = savedResults.find((savedResult, index) => {
    if (editingIndex === index) return false;

    const savedMin = Number(savedResult.min_points);
    const savedMax = Number(savedResult.max_points);

    return (minPointsNum >= savedMin && minPointsNum <= savedMax) ||
      (maxPointsNum >= savedMin && maxPointsNum <= savedMax) ||
      (savedMin >= minPointsNum && savedMin <= maxPointsNum);
  });

  if (overlappingResult) {
    showError("Score ranges cannot overlap with other results", "error");
    return false;
  }

  return true;
};