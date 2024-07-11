<template>
  <div class="container">
    <div class="row">
      <div class="col">
        <form method="post" @submit.prevent="edit()">
          <div class="row">
            <div class="form-group col-md-6 col-lg-4">
              <label for="order_header_id">Id</label>
              <input readonly id="order_header_id" name="id" class="form-control form-control-sm" v-model="orderHeader.id" type="number" required />
              <span v-if="errors.id" class="text-danger">{{errors.id[0]}}</span>
            </div>
            <div class="form-group col-md-6 col-lg-4">
              <label for="order_header_customer_id">Customer</label>
              <select id="order_header_customer_id" name="customer_id" class="form-control form-control-sm" v-model="orderHeader.customer_id" required>
                <option v-for="customer in customers" :key="customer" :value="customer.id" :selected="orderHeader.customer_id == customer.id">{{customer.name}}</option>
              </select>
              <span v-if="errors.customer_id" class="text-danger">{{errors.customer_id[0]}}</span>
            </div>
            <div class="form-group col-md-6 col-lg-4">
              <label for="order_header_order_date">Order Date</label>
              <input id="order_header_order_date" name="order_date" class="form-control form-control-sm" v-model="orderHeader.order_date" data-type="date" autocomplete="off" required />
              <span v-if="errors.order_date" class="text-danger">{{errors.order_date[0]}}</span>
            </div>
            <div class="col-12">
              <router-link class="btn btn-sm btn-secondary" :to="getRef('/orderHeader')">Cancel</router-link>
              <button class="btn btn-sm btn-primary">Submit</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>
<script>
import Service from './Service'
import Util from"../../util"

export default {
  name: 'OrderHeaderEdit',
  data() {
    return {
      orderHeader: {},
      customers: [],
      errors: {}
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
      return Service.edit(this.$route.params.id).then(response => {
        this.orderHeader = response.data.orderHeader
        this.customers = response.data.customers
      })
    },
    edit() {
      Service.edit(this.$route.params.id, this.orderHeader).then(() => {
        this.$router.push(this.getRef('/orderHeader'))
      }).catch((e) => {
        if (e.response.data.errors) {
          this.errors = e.response.data.errors
        }
        else {
          alert(e.response.data.message)
        }
      })
    }
  }
}
</script>
