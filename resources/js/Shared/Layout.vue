<template>
  <div >
    <portal-target name="dropdown" slim />
    <div class="flex flex-col">
      <div class="h-screen flex flex-col" >
        <div class="md:flex flex-shrink-0">
          <div class="bg-indigo-900 md:flex-shrink-0 md:w-56 px-6 py-4 flex items-center justify-between md:justify-center">
            <inertia-link class="mt-1" href="/">
              <logo class="fill-white" width="120" height="28" />
            </inertia-link>
            <div v-click-outside="hideDropdownMenus">
            <dropdown  class="overflow-visible md:hidden" placement="bottom-end">
              <svg @click='togglemenu = !togglemenu' class="fill-white w-6 h-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M0 3h20v2H0V3zm0 6h20v2H0V9zm0 6h20v2H0v-2z" /></svg>
              <b-collapse v-show='togglemenu' class="absolute right-0 left-auto mt-2 px-8 py-4 shadow-lg bg-indigo-800 rounded">
                <div @click="hideDropdownMenus"><main-menu :url="url()"/></div>
              </b-collapse>
            </dropdown>
            </div>
          </div>

          <div class="bg-white border-b w-full p-4 md:py-0 md:px-12 text-sm md:text-md flex justify-between items-center">
            <div class="mt-1 mr-4">{{ $page.auth.user.account.name }}</div> 
            <dropdown class="mt-1" placement="bottom-end">
              <div @click='toggleuser = !toggleuser' v-click-outside="hideDropdownUser" class="flex items-center cursor-pointer select-none group">
                <div class="text-gray-700 group-hover:text-indigo-600 focus:text-indigo-600 mr-1 whitespace-no-wrap">
                  <span>{{ $page.auth.user.first_name }}</span>
                  <span class="hidden md:inline">{{ $page.auth.user.last_name }}</span>
                </div>
                <icon class="w-5 h-5 group-hover:fill-indigo-600 fill-gray-700 focus:fill-indigo-600" name="cheveron-down" />
              </div>
              <b-collapse v-show='toggleuser' class="mr-2" >
              <div class="absolute right-10 mt-2 py-2 shadow-xl bg-white rounded text-sm lg:mr-5 right-0">
                <inertia-link class="block px-6 py-2 hover:bg-indigo-500 hover:text-white" :href="route('users.userprofile', $page.auth.user.id)">My Profile</inertia-link>
                <inertia-link class="block px-6 py-2 hover:bg-indigo-500 hover:text-white" :href="route('users.userprofilepassword', $page.auth.user.id)">Change Password</inertia-link>
                <inertia-link class="block px-6 py-2 hover:bg-indigo-500 hover:text-white" :href="route('access')">Switch Access</inertia-link>
                <inertia-link class="block px-6 py-2 hover:bg-indigo-500 hover:text-white" :href="route('logout')" method="post">Logout</inertia-link>
              </div>
              </b-collapse>
            </dropdown>
          </div>
        </div>


        
        <div class="flex flex-grow overflow-hidden">

          <main-menu :url="url()" class="bg-indigo-800 flex-shrink-0 w-56 px-2 py-4 hidden md:block overflow-y-auto" />
          
          <!-- disini sub menu -->
  
          <div class="flex-1 px-2 py-4 md:p-6 overflow-y-auto" scroll-region>

            <div v-if="$page.auth.listSubMenuByRole" class="flex flex-wrap flex-col border-b-2 border-indigo-300 mb-5">
            <div class="flex flex-wrap mb-3">
            <div v-for="menu in $page.auth.listSubMenuByRole" :key="menu.id">
              <inertia-link class="flex items-center group py-3" :href="route(menu.route)">                
                <button :class="url() == menu.route ? 'mr-5 bg-green-600 cursor-default text-white font-bold py-2 px-6 rounded-lg' : 'mr-5 bg-white text-green-500  hover:text-green-700 font-bold py-2 px-6 rounded-lg'">
                  <i :class="menu.icon  +' text-green mr-3'" ></i> {{menu.name}} <!-- {{url()}} - {{menu.route}} -->
                </button>
              </inertia-link>
            </div>
            </div>
            </div>
            <div v-else ></div>
            
            <flash-messages />
            <slot />
          </div>
        </div>

        


      </div>
    </div>
  </div>
</template>

<script>
import Dropdown from '@/Shared/Dropdown'
import FlashMessages from '@/Shared/FlashMessages'
import Icon from '@/Shared/Icon'
import Logo from '@/Shared/Logo'
import MainMenu from '@/Shared/MainMenu'
import vClickOutside from 'v-click-outside'

export default {
  components: {
    Dropdown,
    FlashMessages,
    Icon,
    Logo,
    MainMenu,
  },
  data() {
    return {
      toggleuser: true,
      togglemenu: true,
      accounts: null,
    }
  },
  methods: {
    url() {      
      return location.pathname.substr(1)
    },
    hideDropdownMenus() {
      this.togglemenu = false
    },
    hideDropdownUser() {
      this.toggleuser = false
    },
  },
  directives: {
      clickOutside: vClickOutside.directive
    },
}

// npm install --save v-click-outside
</script>
