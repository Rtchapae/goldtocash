export const useClipboard = () => {
	const copyToClipboard = async (text) => {
		if (!text) {
			return false
		}

		try {
			await navigator.clipboard.writeText(text)
			return true
		} catch (err) {
			return false
		}
	}

	return {
		copyToClipboard
	}
}

