<template>
  <!-- component -->
  <div class="w-full">
    <nav class="bg-white md:max-w-7xl mx-auto">
      <div class="md:flex items-center justify-between py-2 px-8 md:px-12">
        <div class="flex justify-between items-center">
          <div class="text-2xl font-bold text-gray-800 md:text-3xl">
            <router-link :to="{ name: 'home' }">Simple Resources Management</router-link>
          </div>
          <div class="md:hidden">
            <button type="button" class="block text-gray-800 hover:text-gray-700 focus:text-gray-700 focus:outline-none">
              Simple Resources Management
            </button>
          </div>
        </div>
        <div class="flex flex-col md:flex-row hidden md:block -mx-2">
          <router-link :to="{ name: 'home' }" class="text-gray-800 rounded hover:bg-gray-900 hover:text-gray-100 hover:font-medium py-2 px-2 md:mx-2">
            Home
          </router-link>
          <router-link :to="{ name: 'admin.resources.index' }" class="text-gray-800 rounded hover:bg-gray-900 hover:text-gray-100 hover:font-medium py-2 px-2 md:mx-2">
            Admin
          </router-link>
        </div>
      </div>
    </nav>
    <div class="flex bg-white md:max-w-6xl mx-auto">
      <router-view />
    </div>
  </div>
</template>