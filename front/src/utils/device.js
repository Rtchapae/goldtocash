const MOBILE_UA_RE = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini|Mobile/i

export function isMobileUserAgent(userAgent = '') {
	return MOBILE_UA_RE.test(userAgent)
}
