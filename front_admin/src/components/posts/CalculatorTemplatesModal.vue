<template>
	<TemplateModal modal-id="calculator-templates-modal" title="Calculator Templates">
		<template #body>
			<div class="mb-4">
				<label class="form-label fw-bold">Choose a form style</label>
				<div class="form-check mb-3">
					<input type="checkbox" id="use-checkbox-calc" data-element="use-divider" class="form-check-input" />
					<label for="use-checkbox-calc" class="form-check-label">Use Divider</label>
				</div>
			</div>

			<div class="mb-4">
				<h6 class="fw-bold mb-2">Right Side</h6>
				<TemplateCodeBlock
					element-id="right-side-pre"
					:code="rightSideCode"
				/>
			</div>

			<hr class="my-4" />

			<div class="mb-3">
				<h6 class="fw-bold mb-2">Centered</h6>
				<TemplateCodeBlock
					element-id="centered-pre"
					:code="centeredCode"
				/>
			</div>
		</template>
	</TemplateModal>
</template>

<script setup>
import TemplateModal from './TemplateModal.vue'
import TemplateCodeBlock from './TemplateCodeBlock.vue'

const rightSideCode = `<div class="row my-4">
  <div class="col-md-7" data-element="left">
    {INSERT YOUR CONTENT HERE}
  </div>
  <div class="col-md-5" data-element="right">
    <iframe style="border: none;width:100%;" src="/i/gold-calculator"></iframe>
  </div>
</div>`

const centeredCode = `<div class="row my-4">
  <div class="col-md-12 mx-auto">
    <iframe style="border: none;width:100%;" src="/i/gold-calculator"></iframe>
  </div>
</div>`
</script>

