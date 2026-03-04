import axios from 'axios'

const API_BASE_URL = import.meta.env.VITE_API_BASE_URL || '/api'

class ContactApi {
  constructor(baseUrl) {
    this.client = axios.create({
      baseURL: baseUrl,
      headers: {
        'Content-Type': 'application/json',
      },
    })
  }

  getErrorMessage(error) {
    return error?.response?.data?.message || error?.message || 'Request failed.'
  }

  async getContacts() {
    try {
      const { data } = await this.client.get('/contacts')
      return data
    } catch (error) {
      throw new Error(this.getErrorMessage(error))
    }
  }

  async getContact(id) {
    try {
      const { data } = await this.client.get(`/contacts/${id}`)
      return data
    } catch (error) {
      throw new Error(this.getErrorMessage(error))
    }
  }

  async createContact(payload) {
    try {
      const { data } = await this.client.post('/contacts', payload)
      return data
    } catch (error) {
      throw new Error(this.getErrorMessage(error))
    }
  }

  async updateContact(id, payload) {
    try {
      const { data } = await this.client.put(`/contacts/${id}`, payload)
      return data
    } catch (error) {
      throw new Error(this.getErrorMessage(error))
    }
  }

  async deleteContact(id) {
    try {
      const { data } = await this.client.delete(`/contacts/${id}`)
      return data
    } catch (error) {
      throw new Error(this.getErrorMessage(error))
    }
  }
}

export const contactApi = new ContactApi(API_BASE_URL)
