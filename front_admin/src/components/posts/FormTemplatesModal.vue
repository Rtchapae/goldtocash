<template>
	<TemplateModal modal-id="form-templates-modal" title="Form Templates">
		<template #body>
			<div class="mb-4">
				<label class="form-label fw-bold">Choose a form style</label>
				<select data-element="form-name-selector" class="form-select mb-3">
					<option value="traditional">Traditional</option>
					<option value="sophisticated">Sophisticated</option>
					<option value="modern" selected>Modern</option>
				</select>
				<div class="form-check">
					<input type="checkbox" id="use-checkbox" data-element="use-divider" class="form-check-input" />
					<label for="use-checkbox" class="form-check-label">Use Divider</label>
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
    <iframe style="border: none;width:100%;" src="/i/modern-form"></iframe>
  </div>
</div>`

const centeredCode = `<div class="row my-4">
  <div class="col-md-6 mx-auto">
    <iframe style="border: none;width:100%;" src="/i/modern-form"></iframe>
  </div>
</div>`
</script>

