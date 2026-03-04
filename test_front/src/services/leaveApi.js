import axios from 'axios'

const API_BASE_URL = import.meta.env.VITE_API_BASE_URL || '/api'

class LeaveApi {
  constructor(baseUrl) {
    this.client = axios.create({
      baseURL: baseUrl,
      headers: {
        'Content-Type': 'application/json',
      },
    })
  }

  getErrorMessage(error) {
    if (error?.response?.data?.errors) {
      const firstError = Object.values(error.response.data.errors)[0]
      if (Array.isArray(firstError) && firstError.length > 0) {
        return firstError[0]
      }
    }

    return error?.response?.data?.message || error?.message || 'Request failed.'
  }

  async calculateLeaveBalance(payload) {
    try {
      const { data } = await this.client.post('/leave-balance/calculate', payload)
      return data
    } catch (error) {
      throw new Error(this.getErrorMessage(error))
    }
  }
}

export const leaveApi = new LeaveApi(API_BASE_URL)
