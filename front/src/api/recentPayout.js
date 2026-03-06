import { get } from './client'

export const getRecentPayout = () => {
  return get('recent-payout')
}
