<template>
  <div class="container">
    <div class="row">
      <div class="col">
        <div>
          <div class="row">
            <div class="form-group col-md-6 col-lg-4">
              <label for="brand_id">Id</label>
              <input readonly id="brand_id" name="id" class="form-control form-control-sm" :value="brand.id" type="number" required />
            </div>
            <div class="form-group col-md-6 col-lg-4">
              <label for="brand_name">Name</label>
              <input readonly id="brand_name" name="name" class="form-control form-control-sm" :value="brand.name" required maxlength="50" />
            </div>
            <div class="col-12">
              <h6>Brand's products</h6>
              <table class="table table-sm table-striped table-hover">
                <thead>
                  <tr>
                    <th>Product Name</th>
                    <th>Product Price</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="brandProduct in brandProducts" :key="brandProduct">
                    <td>{{brandProduct.name}}</td>
                    <td class="text-right">{{brandProduct.price}}</td>
                    <td class="text-center">
                      <router-link class="btn btn-sm btn-secondary" :to="`/product/${brandProduct.id}`" title="View"><i class="fa fa-eye"></i></router-link>
                      <router-link class="btn btn-sm btn-primary" :to="`/product/edit/${brandProduct.id}`" title="Edit"><i class="fa fa-pencil"></i></router-link>
                      <a class="btn btn-sm btn-danger" href="#!" @click.prevent="deleteItem(`products/${brandProduct.id}`)" title="Delete"><i class="fa fa-times"></i></a>
                    </td>
                  </tr>
                </tbody>
              </table>
              <router-link class="btn btn-sm btn-primary" :to="`/product/create?product_brand_id=${brand.id}`">Add</router-link>
              <hr />
            </div>
            <div class="col-12">
              <router-link class="btn btn-sm btn-secondary" :to="getRef('/brand')">Back</router-link>
              <router-link class="btn btn-sm btn-primary" :to="`/brand/edit/${brand.id}?ref=${encodeURIComponent(getRef('/brand'))}`">Edit</router-link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import Service from './Service'
import Util from"../../util"
import http from '../../http'

export default {
  name: 'BrandDetail',
  data() {
    return {
      brand: {},
      brandProducts: [],
    }
  },
  mounted() {
    this.get().finally(() => {
      this.initView(true)
    })
  },
  methods: {
    ...Util,
    get() {
      return Service.get(this.$route.params.id).then(response => {
        this.brand = response.data.brand
        this.brandProducts = response.data.brandProducts
      })
    }
    ,deleteItem(url) {
      if (confirm('Delete this item?')) {
        http.delete(url).then(() => {
          this.get()
        }).catch((e) => {
          alert(e.response.data.message)
        })
      }
    }
  }
}
</script>
