import http from '../../http'

export default {
  get(id) {
    if (id) {
      return http.get(`/products/${id}`)
    }
    else {
      return http.get('/products' + location.search)
    }
  },
  create(data) {
    if (data) {
      return http.post('/products', data)
    }
    else {
      return http.get('/products/create')
    }
  },
  edit(id, data) {
    if (data) {
      return http.post(`/products/${id}`, data)
    }
    else {
      return http.get(`/products/${id}/edit`)
    }
  },
  delete(id, data) {
    if (data) {
      return http.delete(`/products/${id}`)
    }
    else {
      return http.get(`/products/${id}/delete`)
    }
  }
}