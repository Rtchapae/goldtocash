import { get } from './client'

export const getTrustpilotReviews = () => {
  return get('trustpilot/reviews')
}
