<template>
  <div class="w-full mx-auto flex flex-col place-items-center ">
    <div v-for="(field, index) in props.inputForms" :key="index" class="max-w-md w-full">
      <InputField :placeholder="field.label" :inputId="field.type" :inputType="getInputType(field.type)"
        :inputValue="fieldsData[index].input_value" :error="fieldsData[index].error" :required="field.is_required"
        @emittedKeyPressEnter="emitKeyPressEnter" @update:response="updateFieldsData" text-color="black"
        :borderColor="quizTheme.button_color" class="mb-[2.5px] rounded-md" />
    </div>
  </div>
</template>

<script setup>
import { storeToRefs } from 'pinia';
import { ref, watch, onMounted } from 'vue';
import { useQuizzesStore } from '@/stores/quizzesStore';
import { useRoute } from 'vue-router';
import { validateEmail, validatePhone, validateText, validateAge } from '@/utils/formValidation';
import InputField from './ui/InputField.vue';

const route = useRoute();
const quizIdParam = ref(route.params.quizId);

const props = defineProps({
  inputForms: {
    type: Array,
    required: true,
  },
  acceptedTerms: {
    type: Boolean,
    required: true,
  },
});

const store = useQuizzesStore();
const { quizTheme } = storeToRefs(store);

const fieldsData = ref(
  Array.isArray(props.inputForms)
    ? props.inputForms.map((field) => {
      const storedData =
        JSON.parse(sessionStorage.getItem(`getResult_${quizIdParam.value}`)) || [];
      const inputField = storedData.find((item) => item.input_type === field.type);
      return {
        input_type: field.type,
        input_value: inputField ? inputField.input_value : '',
      };
    })
    : []
);

const emit = defineEmits([
  'update:fieldsData',
  'update:areFieldsPopulated',
  'emittedKeyPressEnter',
  'emittedIsTermsAccepted',
]);

const validateAcceptedTerms = () => {
  if (!props.acceptedTerms) {
    emit('emittedIsTermsAccepted', true);
    return true;
  }
  emit('emittedIsTermsAccepted', false);
  return false;
};

const validateFields = () => {
  let hasErrors = false;

  fieldsData.value.forEach((field, index) => {
    field.error = '';
    const inputForm = props.inputForms[index];
    const value = field.input_value;

    if (inputForm.is_required || (value !== '' && value != null)) {
      if (value === '' || value == null) {
        field.error = 'This field is required';
        hasErrors = true;
        return;
      }

      let errorMessage = null;
      switch (field.input_type) {
        case 'email':
          errorMessage = validateEmail(value);
          break;
        case 'tel':
          errorMessage = validatePhone(value);
          break;
        case 'text':
          errorMessage = validateText(value);
          break;
        case 'age':
          errorMessage = validateAge(value);
          break;
        default:
          break;
      }

      if (errorMessage) {
        field.error = errorMessage;
        hasErrors = true;
      }
    }
  });

  hasErrors = validateAcceptedTerms() || hasErrors;

  return !hasErrors;
};

const emitKeyPressEnter = () => {
  emit('emittedKeyPressEnter');
};

defineExpose({ validateFields });

const updateFieldsData = (fieldType, inputValue) => {
  const fieldIndex = fieldsData.value.findIndex((item) => item.input_type === fieldType);
  if (fieldIndex !== -1) {
    fieldsData.value[fieldIndex].input_value = inputValue;
    emit('update:fieldsData', fieldsData.value);
  }
};

function getInputType(type) {
  switch (type) {
    case 'email':
      return 'email';
    case 'tel':
      return 'tel';
    case 'text':
      return 'text';
    case 'age':
      return 'number';
    default:
      return 'text';
  }
}

fieldsData.value.forEach((field, index) => {
  watch(
    () => field.input_value,
    (newValue) => {
      if (field.error && newValue) {
        field.error = '';
      }
    }
  );
});

onMounted(() => {
  checkFieldsCompletion();
});

const checkFieldsCompletion = () => {
  const storedData = JSON.parse(sessionStorage.getItem(`getResult_${quizIdParam.value}`)) || [];

  const areRequiredFieldsFilled = props.inputForms.every((form) => {
    if (!form.is_required) return true;

    const storedField = storedData.find((item) => item.input_type === form.type);
    return storedField?.input_value && storedField.input_value.trim() !== '';
  });

  emit('update:areFieldsPopulated', areRequiredFieldsFilled);
};

watch(
  () => fieldsData.value,
  () => checkFieldsCompletion(),
  { deep: true }
);
</script>
