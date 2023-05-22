<template>
  <div >


   


      <div v-for="menu in $page.auth.listMenuByRole" :key="menu.id">

         <div class="mb-1">
          <inertia-link class="flex items-center group py-3" :href="route(menu.route)">
            
            <i :class="menu.icon  +' text-white mr-3'" ></i>
            <div :class="isUrl(menu.route) ? 'text-white' 
            : 'text-indigo-300 group-hover:text-white '"> {{menu.name}}</div>
          </inertia-link>
        </div>
    
    </div>
   
   

    <!--
       <div class="mb-1">
      <inertia-link class="flex items-center group py-3" :href="route('dashboard')">
        <icon name="dashboard" class="w-4 h-4 mr-2" :class="isUrl('') ? 'fill-white' : 'fill-indigo-400 group-hover:fill-white'" />
        <div :class="isUrl('') ? 'text-white' : 'text-indigo-300 group-hover:text-white'">Dashboard</div>
      </inertia-link>
    </div>

    <div class="mb-1">
      <inertia-link class="flex items-center group py-3" :href="route('usertypes')">
        <icon name="setting" class="w-4 h-4 mr-2" :class="isUrl('usertypes') ? 'fill-white' : 'fill-indigo-400 group-hover:fill-white'" />
        <div :class="isUrl('usertypes') ? 'text-white' : 'text-indigo-300 group-hover:text-white'">Tipe User</div>
      </inertia-link>
    </div>


    <div class="mb-1">
      <inertia-link class="flex items-center group py-3" :href="route('groups')">
        <icon name="setting" class="w-4 h-4 mr-2" :class="isUrl('usertypes') ? 'fill-white' : 'fill-indigo-400 group-hover:fill-white'" />
        <div :class="isUrl('groups') ? 'text-white' : 'text-indigo-300 group-hover:text-white'">Groups</div>
      </inertia-link>
    </div>

    <div class="mb-1">
      <inertia-link class="flex items-center group py-3" :href="route('group_users')">
        <icon name="users" class="w-4 h-4 mr-2" :class="isUrl('group-users') ? 'fill-white' : 'fill-indigo-400 group-hover:fill-white'" />
        <div :class="isUrl('group-users') ? 'text-white' : 'text-indigo-300 group-hover:text-white'">Grup User</div>
      </inertia-link>
    </div>

    <div class="mb-1">
      <inertia-link class="flex items-center group py-3" :href="route('group_courses')">
        <icon name="office" class="w-4 h-4 mr-2" :class="isUrl('group_courses') ? 'fill-white' : 'fill-indigo-400 group-hover:fill-white'" />
        <div :class="isUrl('group_courses') ? 'text-white' : 'text-indigo-300 group-hover:text-white'">Grup Course</div>
      </inertia-link>
    </div>

    <div class="mb-1">
      <inertia-link class="flex items-center group py-3" :href="route('subjects')">
        <icon name="office" class="w-4 h-4 mr-2" :class="isUrl('subjects') ? 'fill-white' : 'fill-indigo-400 group-hover:fill-white'" />
        <div :class="isUrl('subjects') ? 'text-white' : 'text-indigo-300 group-hover:text-white'">Subjects</div>
      </inertia-link>
    </div>
    <div class="mb-1">
      <inertia-link class="flex items-center group py-3" :href="route('users')">
        <icon name="office" class="w-4 h-4 mr-2" :class="isUrl('users') ? 'fill-white' : 'fill-indigo-400 group-hover:fill-white'" />
        <div :class="isUrl('users') ? 'text-white' : 'text-indigo-300 group-hover:text-white'">Users</div>
      </inertia-link>
    </div>
    <div class="mb-1">
      <inertia-link class="flex items-center group py-3" :href="route('import-users')">
        <icon name="office" class="w-4 h-4 mr-2" :class="isUrl('import-users') ? 'fill-white' : 'fill-indigo-400 group-hover:fill-white'" />
        <div :class="isUrl('import-users') ? 'text-white' : 'text-indigo-300 group-hover:text-white'">Import Users</div>
      </inertia-link>
    </div>
    <div class="mb-1">
      <inertia-link class="flex items-center group py-3" :href="route('menus')">
        <icon name="office" class="w-4 h-4 mr-2" :class="isUrl('menus') ? 'fill-white' : 'fill-indigo-400 group-hover:fill-white'" />
        <div :class="isUrl('menus') ? 'text-white' : 'text-indigo-300 group-hover:text-white'">Menus</div>
      </inertia-link>
    </div>
    <div class="mb-1">
      <inertia-link class="flex items-center group py-3" :href="route('categories.index')">
        <icon name="office" class="w-4 h-4 mr-2" :class="isUrl('categories') ? 'fill-white' : 'fill-indigo-400 group-hover:fill-white'" />
        <div :class="isUrl('categories') ? 'text-white' : 'text-indigo-300 group-hover:text-white'">Kategori</div>
      </inertia-link>
    </div>
    <div class="mb-1">
      <inertia-link class="flex items-center group py-3" :href="route('levels.index')">
        <icon name="office" class="w-4 h-4 mr-2" :class="isUrl('levels') ? 'fill-white' : 'fill-indigo-400 group-hover:fill-white'" />
        <div :class="isUrl('levels') ? 'text-white' : 'text-indigo-300 group-hover:text-white'">Level</div>
      </inertia-link>
    </div>
    <div class="mb-1">
      <inertia-link class="flex items-center group py-3" :href="route('courses.index')">
        <icon name="office" class="w-4 h-4 mr-2" :class="isUrl('courses') ? 'fill-white' : 'fill-indigo-400 group-hover:fill-white'" />
        <div :class="isUrl('courses') ? 'text-white' : 'text-indigo-300 group-hover:text-white'">Courses</div>
      </inertia-link>
    </div>
    <div class="mb-1">
      <inertia-link class="flex items-center group py-3" :href="route('settings')">
        <icon name="setting" class="w-4 h-4 mr-2" :class="isUrl('') ? 'fill-white' : 'fill-indigo-400 group-hover:fill-white'" />
        <div :class="isUrl('') ? 'text-white' : 'text-indigo-300 group-hover:text-white'">Settings</div>
      </inertia-link>
    </div>
    <div class="mb-1">
      <inertia-link class="flex items-center group py-3" :href="route('organizations')">
        <icon name="office" class="w-4 h-4 mr-2" :class="isUrl('organizations') ? 'fill-white' : 'fill-indigo-400 group-hover:fill-white'" />
        <div :class="isUrl('organizations') ? 'text-white' : 'text-indigo-300 group-hover:text-white'">Organizations</div>
      </inertia-link>
    </div>
    <div class="mb-1">
      <inertia-link class="flex items-center group py-3" :href="route('contacts')">
        <icon name="users" class="w-4 h-4 mr-2" :class="isUrl('contacts') ? 'fill-white' : 'fill-indigo-400 group-hover:fill-white'" />
        <div :class="isUrl('contacts') ? 'text-white' : 'text-indigo-300 group-hover:text-white'">Contacts</div>
      </inertia-link>
    </div>
    <div class="mb-1">
      <inertia-link class="flex items-center group py-3" :href="route('reports')">
        <icon name="printer" class="w-4 h-4 mr-2" :class="isUrl('reports') ? 'fill-white' : 'fill-indigo-400 group-hover:fill-white'" />
        <div :class="isUrl('reports') ? 'text-white' : 'text-indigo-300 group-hover:text-white'">Reports</div>
      </inertia-link>
    </div>

    -->
  </div>
</template>

<script>
import Icon from '@/Shared/Icon'

export default {
  components: {
    Icon,
  },
  props: {
    url: String
  },
  methods: {
    isUrl(...urls) {
      if (urls[0] === '') {
        return this.url === ''
      }

      return urls.filter(url => this.url.startsWith(url)).length
    },
  },
}
</script>
