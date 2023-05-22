<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('usertypes')">{{menuName}}</inertia-link>
      <span class="text-indigo-400 font-medium">/</span>
      {{ form.name }}
    </h1>
    <div class="bg-white rounded shadow overflow-hidden max-w-3xl">
      <form @submit.prevent="submit">
        <div v-if="usertype.id<=5" class="p-8 -mr-6 -mb-8 flex flex-wrap">
         
          <text-input readonly="TRUE" type="text" v-model="form.name" :errors="$page.errors.name" class="pr-6 pb-8 w-full lg:w-1/1" label="Usertypes Name" />
        
        </div>
        <div v-else class="p-8 -mr-6 -mb-8 flex flex-wrap">
         
          <text-input type="text" v-model="form.name" :errors="$page.errors.name" class="pr-6 pb-8 w-full lg:w-1/1" label="Usertype Name" />
        
        </div>


        <div  class="ml-10 mb-3 "  v-for="menu in listMenus" v-bind:key="menu.id">          
            
            <label class="md:w-2/3 block ">
              <input class="mr-2 leading-tight" @change="onChange(menu, menu.menu_id)" v-model="menu.menu_id" type="checkbox" @click="checkboxToggle(menu.id)">
              <span class="text-sm">
                {{menu.name}}
              </span>
            </label>

                <label v-show="menu.menu_id" class="md:w-2/3 block ml-6 mt-3" v-for="child in menu.children" v-bind:key="child.id">
                  <input class="mr-2 leading-tight" v-model="child.menu_id" type="checkbox" @click="checkboxToggle(child.id)">
                  <span class="text-sm">
                   {{child.name}}
                  </span>
                </label>

        </div>



        <div class="px-8 py-4 bg-gray-100 border-t border-gray-200 flex items-center">
          <button v-if="usertype.is_active&&usertype.id>5" class="text-red-600 hover:underline" tabindex="-1" type="button" @click="destroy()">Hapus {{menuName}}</button>
          <loading-button :loading="sending" class="btn-indigo ml-auto" type="submit">Update Usertype</loading-button>
        </div>
      </form>
    </div>
    
  </div>
</template>

<script>
import Icon from '@/Shared/Icon'
import Layout from '@/Shared/Layout'
import LoadingButton from '@/Shared/LoadingButton'
import SelectInput from '@/Shared/SelectInput'
import TextInput from '@/Shared/TextInput'
import TrashedMessage from '@/Shared/TrashedMessage'

export default {
  metaInfo() {
    return { title: this.form.name }
  },
  layout: Layout,
  components: {
    Icon,
    LoadingButton,
    SelectInput,
    TextInput,
    TrashedMessage,
  },
  props: {
    usertype: Object,
    menuName: String,
    listMenus: Array,
    users: Array
  },
  remember: 'form',
  data() {
    return {
      sending: false,
      form: {
        name: this.usertype.name,
      },
    }
  },
  watch: {
    grand_parent: function (val) {
      if (val) this.parent = true;
    }
  },
  methods: {
    submit() {
      this.sending = true
      this.$inertia.put(this.route('usertypes.update', this.usertype.id), this.form)
        .then(() => this.sending = false)
    },
    destroy() {
      if (confirm('Apakah anda yakin akan menghapus data '+this.form.name+' ..?')) {
        this.$inertia.delete(this.route('usertypes.destroy', this.usertype.id))
      }
    },
    checkboxToggle (id) {

      this.$inertia.visit(this.route('usertypes.store_menu'), {
        method: 'post',
        data: {
           usertype_id: this.usertype.id,
           menu_id: id

        },
        replace: true,
        preserveState: true,
        preserveScroll: true,
        only: [],
      })

     
    },
    onChange(item, state){

        if(state){
          console.log(state);
          for(let child of item.children){
              child.menu_id = state
          }
        }
        
    }
  },
}
</script>
