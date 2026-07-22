export const BLOCK_TYPES = [
	{ type: 'heading', label: 'Heading', icon: 'solar:text-bold-outline' },
	{ type: 'text', label: 'Text', icon: 'solar:document-text-outline' },
	{ type: 'image', label: 'Image', icon: 'solar:gallery-outline' },
	{ type: 'text_image', label: 'Text + Image', icon: 'solar:widget-2-outline' },
	{ type: 'cta', label: 'Button (CTA)', icon: 'solar:cursor-outline' },
	{ type: 'kit_form', label: 'Kit request form', icon: 'solar:clipboard-list-outline' },
	{ type: 'gold_calculator', label: 'Gold calculator', icon: 'solar:calculator-outline' },
	{ type: 'spacer', label: 'Spacer', icon: 'solar:align-vertical-spacing-outline' },
	{ type: 'divider', label: 'Divider', icon: 'solar:minus-circle-outline' },
]

export function createBlockId() {
	return `block-${Date.now()}-${Math.random().toString(36).slice(2, 8)}`
}

export function createDefaultBlock(type) {
	const id = createBlockId()
	switch (type) {
		case 'heading':
			return { id, type, data: { text: 'Your heading', level: 2, align: 'left', color: '' } }
		case 'text':
			return { id, type, data: { content: '<p>Write your text here.</p>', fontSize: 'md', align: 'left' } }
		case 'image':
			return { id, type, data: { src: '', alt: '', caption: '', width: 'full', align: 'center' } }
		case 'text_image':
			return { id, type, data: { title: '', text: '<p>Describe your offer.</p>', image: '', imagePosition: 'right' } }
		case 'cta':
			return { id, type, data: { text: 'Get started', url: '/', style: 'primary', align: 'center' } }
		case 'kit_form':
			return { id, type, data: { layout: 'centered', title: 'Request your free appraisal kit' } }
		case 'gold_calculator':
			return { id, type, data: { layout: 'centered', showHeading: true } }
		case 'spacer':
			return { id, type, data: { height: 'md' } }
		case 'divider':
			return { id, type, data: { style: 'line' } }
		default:
			return { id, type: 'text', data: { content: '', fontSize: 'md', align: 'left' } }
	}
}

export const FONT_SIZE_OPTIONS = [
	{ value: 'sm', label: 'Small' },
	{ value: 'md', label: 'Medium' },
	{ value: 'lg', label: 'Large' },
]

export const ALIGN_OPTIONS = [
	{ value: 'left', label: 'Left' },
	{ value: 'center', label: 'Center' },
	{ value: 'right', label: 'Right' },
]

export const LAYOUT_WIDTH_OPTIONS = [
	{ value: 'centered', label: 'Centered (narrow)' },
	{ value: 'full', label: 'Full width' },
]
	{ value: 'sm', label: 'Small (24px)' },
	{ value: 'md', label: 'Medium (48px)' },
	{ value: 'lg', label: 'Large (72px)' },
	{ value: 'xl', label: 'Extra large (96px)' },
]
