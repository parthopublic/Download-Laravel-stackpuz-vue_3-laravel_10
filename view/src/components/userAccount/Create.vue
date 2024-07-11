<template>
  <div class="container">
    <div class="row">
      <div class="col">
        <form method="post" @submit.prevent="create()">
          <div class="row">
            <div class="form-group col-md-6 col-lg-4">
              <label for="user_account_name">Name</label>
              <input id="user_account_name" name="name" class="form-control form-control-sm" v-model="userAccount.name" required maxlength="50" />
              <span v-if="errors.name" class="text-danger">{{errors.name[0]}}</span>
            </div>
            <div class="form-group col-md-6 col-lg-4">
              <label for="user_account_email">Email</label>
              <input id="user_account_email" name="email" class="form-control form-control-sm" v-model="userAccount.email" type="email" required maxlength="50" />
              <span v-if="errors.email" class="text-danger">{{errors.email[0]}}</span>
            </div>
            <div class="form-check col-md-6 col-lg-4">
              <input id="user_account_active" name="active" class="form-check-input" type="checkbox" v-model="userAccount.active" :checked="userAccount.active" />
              <label class="form-check-label" for="user_account_active">Active</label>
              <span v-if="errors.active" class="text-danger">{{errors.active[0]}}</span>
            </div>
            <div class="col-12">
              <h6>
              </h6><label>Roles</label>
              <div v-for="role in roles" :key="role" class="form-check">
                <input :id="`user_role_role_id${role.id}`" name="role_id" class="form-check-input" type="checkbox" :value="role.id" />
                <label class="form-check-label" :for="`user_role_role_id${role.id}`">{{role.name}}</label>
              </div>
            </div>
            <div class="col-12">
              <router-link class="btn btn-sm btn-secondary" :to="getRef('/userAccount')">Cancel</router-link>
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
  name: 'UserAccountCreate',
  data() {
    return {
      userAccount: {},
      roles: [],
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
      return Service.create().then(response => {
        this.roles = response.data.roles
      })
    },
    create() {
      this.userAccount.role_id = Array.from(document.querySelectorAll('[name="role_id"]:checked')).map(e => e.value)
      Service.create(this.userAccount).then(() => {
        this.$router.push(this.getRef('/userAccount'))
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
