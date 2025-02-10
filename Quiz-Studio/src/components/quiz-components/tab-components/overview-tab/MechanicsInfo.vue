<template>
  <v-card class="mb-6 border rounded-lg" width="100%" elevation="0" v-if="props.quizStatus.status === 'Unpublished' && props.adminRole === 1">
    <div class="d-flex justify-space-between align-center px-6 pt-4">
      <h1 class="text-h6 font-weight-bold">
        Mechanics
      </h1>
      <v-btn color="primary" prepend-icon="mdi-plus" @click="addNewInstructionField()" variant="tonal">
        Add Instruction
      </v-btn>
    </div>
    <v-card-text>
      <div class="info-grid">
        <div class="info-item" v-for="(instruction, index) in localInstructionsInput" :key="index">
          <div class="info-label">Instruction {{ index + 1 }}</div>
          <div class="info-content">
            <v-text-field v-model="instruction.instruction" append-inner-icon="mdi-close" @change="debouncedSave"
              @click:append-inner="handleInstructionDelete(instruction)" @keyup.enter="handleEnterPress"
              variant="outlined" class="mb-3" counter maxlength="70">
            </v-text-field>
          </div>
        </div>
      </div>
    </v-card-text>
  </v-card>

  <v-card class="mb-6 border rounded-lg" width="100%" elevation="0" v-else>
    <v-card-title class="text-h6 font-weight-bold px-6 pt-4">
      Mechanics
    </v-card-title>

    <v-card-text>
      <div v-for="(instruction, index) in localInstructionsInput" :key="index">
        <p>
          <span class="circle-label me-3 mb-3 bg-grey-lighten-2">{{ index + 1 }}</span>
          {{ instruction.instruction }}
        </p>
      </div>

    </v-card-text>


  </v-card>
</template>

<script setup>
import { defineProps, ref, watch } from "vue";
import { getAuth } from "@/pages/auth/authService";
import axios from "axios";
import { debounce } from "lodash";


const emit = defineEmits(['updateMechanics'])
const { token } = getAuth();
const props = defineProps({
  quizId: Number,
  quizStatus: Object,
  mechanicId: Number,
  mechanicInstruction: {
    type: Object,
    required: true,
  },
  adminRole: {
    type: Number,
    required: true
  }
});


const localInstructionsInput = ref([]);
const hasEmptyInstruction = ref(false)
const hasNewInstruction = ref(false)
const mechanics = ref({
  mechanicInstructions: null,
})

watch(() => props.mechanicInstruction, (newValue) => {
  if (newValue) {
    localInstructionsInput.value = newValue.map((instruction) => ({
      status: 'old',
      instruction_id: instruction.instruction_id,
      instruction: instruction.mechanic_instruction.instruction,
    }));
  }
}, { immediate: true });


const addNewInstructionField = () => {
  hasEmptyInstruction.value = localInstructionsInput.value.some(item => !item.instruction || item.instruction === "");
  if (!hasEmptyInstruction.value && localInstructionsInput.value.length < 5) {
    const newInstruction = {
      status: 'new',
      instruction: null
    };
    hasNewInstruction.value = true;
    localInstructionsInput.value.push(newInstruction);
  } else if (hasEmptyInstruction) {
    window.$snackbar("You cannot add a new instruction with a missing or empty value", "error");
  } else {
    window.$snackbar("You can only add up to 5 instructions", "error");
  }
};


const handleInstructionDelete = async (instruction) => {
  if (instruction.status === 'new') {
    localInstructionsInput.value = localInstructionsInput.value.filter(
      item => item !== instruction
    );
  } else {
    await deleteMechanicInstructions(instruction.instruction_id);
  }
}

const deleteMechanicInstructions = async (instructionId) => {
  if(localInstructionsInput.value.length === 1){
    window.$snackbar("Delete Failed: atleast 1 instruction is required", "error");
    return
  }

  localInstructionsInput.value = localInstructionsInput.value.filter(
    (instruction) => instruction.instruction_id !== instructionId
  );

  try {
    const response = await axios.delete(`api/mechanic-pages/${props.mechanicId}/${instructionId}`, {
      headers: {
        Authorization: `Bearer ${token}`
      }
    });
    window.$snackbar("Instruction deleted successfully", "success");
  } catch (error) {
    console.error("Failed to delete instruction:", error);
    window.$snackbar("Failed to delete instruction", "error");
  }
}


const updateMechanic = async () => {
  mechanics.value.mechanicInstructions = localInstructionsInput.value;
  if(hasNewInstruction.value){
    mechanics.value.mechanicInstructions = mechanics.value.mechanicInstructions.filter(item => item.instruction !== null);
  }
  
  const hasEmptyInstruction = (mechanics.value.mechanicInstructions.some(obj => obj.instruction.trim() === '') || false);
  if(hasEmptyInstruction){
    localInstructionsInput.value =
    props.mechanicInstruction.map((item) => ({
      status: 'old',
      instruction_id: item.instruction_id,
      instruction: item.mechanic_instruction.instruction,
    }))
    window.$snackbar('Instruction/s is Required', 'error')
    return
  }
 
  try {
    const response = await axios.post(`api/mechanic-pages/${props.mechanicId}`, mechanics.value, {
      headers: {
        Authorization: `Bearer ${token}`
      }
    });

    emit('updateMechanics', {
      id: response.data.id,
      instruction: response.data.instructions
    })

    if(hasNewInstruction.value){
      localInstructionsInput.value.push({
        status: 'new',
        instruction: null
      })
    }
    hasNewInstruction.value = false;

    window.$snackbar("Mechanic updated successfully", "success");
  } catch (error) {
    console.error("Failed to update mechanic:", error);
    window.$snackbar("Failed to update mechanic", "error");
  }
}

const debouncedSave = debounce(() => {
  updateMechanic();
}, 500);

const handleEnterPress = (event) => {
  debouncedSave.flush();
  event.target.blur();
};

</script>

<style scope>
.circle-label {
  display: inline-block;
  width: 20px;
  height: 20px;
  line-height: 20px;
  text-align: center;
  border-radius: 50%;
}

.info-grid {
  display: grid;
  padding: 1rem;
}

.info-item {
  display: grid;
  gap: 0.5rem;
}

.info-label {
  font-weight: 500;
  color: #616161;
  font-size: 12px;
}

.info-content {
  min-height: 40px;
}
</style>
