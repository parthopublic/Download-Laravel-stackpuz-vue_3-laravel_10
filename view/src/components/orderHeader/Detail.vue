<template>
  <div class="container">
    <div class="row">
      <div class="col">
        <div>
          <div class="row">
            <div class="form-group col-md-6 col-lg-4">
              <label for="order_header_id">Id</label>
              <input readonly id="order_header_id" name="id" class="form-control form-control-sm" :value="orderHeader.id" type="number" required />
            </div>
            <div class="form-group col-md-6 col-lg-4">
              <label for="customer_name">Customer</label>
              <input readonly id="customer_name" name="customer_name" class="form-control form-control-sm" :value="orderHeader.customer_name" maxlength="50" />
            </div>
            <div class="form-group col-md-6 col-lg-4">
              <label for="order_header_order_date">Order Date</label>
              <input readonly id="order_header_order_date" name="order_date" class="form-control form-control-sm" :value="orderHeader.order_date" data-type="date" autocomplete="off" required />
            </div>
            <div class="col-12">
              <table class="table table-sm table-striped table-hover">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="orderHeaderOrderDetail in orderHeaderOrderDetails" :key="orderHeaderOrderDetail">
                    <td class="text-center">{{orderHeaderOrderDetail.no}}</td>
                    <td>{{orderHeaderOrderDetail.product_name}}</td>
                    <td class="text-right">{{orderHeaderOrderDetail.qty}}</td>
                    <td class="text-center">
                      <router-link class="btn btn-sm btn-primary" :to="`/orderDetail/edit/${orderHeaderOrderDetail.order_id}/${orderHeaderOrderDetail.no}`" title="Edit"><i class="fa fa-pencil"></i></router-link>
                      <a class="btn btn-sm btn-danger" href="#!" @click.prevent="deleteItem(`orderDetails/${orderHeaderOrderDetail.order_id}/${orderHeaderOrderDetail.no}`)" title="Delete"><i class="fa fa-times"></i></a>
                    </td>
                  </tr>
                </tbody>
              </table>
              <router-link class="btn btn-sm btn-primary" :to="`/orderDetail/create?order_detail_order_id=${orderHeader.id}`">Add</router-link>
              <hr />
            </div>
            <div class="col-12">
              <router-link class="btn btn-sm btn-secondary" :to="getRef('/orderHeader')">Back</router-link>
              <router-link class="btn btn-sm btn-primary" :to="`/orderHeader/edit/${orderHeader.id}?ref=${encodeURIComponent(getRef('/orderHeader'))}`">Edit</router-link>
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
  name: 'OrderHeaderDetail',
  data() {
    return {
      orderHeader: {},
      orderHeaderOrderDetails: [],
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
        this.orderHeader = response.data.orderHeader
        this.orderHeaderOrderDetails = response.data.orderHeaderOrderDetails
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
