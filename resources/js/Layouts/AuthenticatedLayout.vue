<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="bg-[#1E1E1E] text-white">
        <div class="flex">
            <!-- Sidebar for Large Screens -->
            <nav class="hidden lg:block w-60 bg-black p-4 min-h-screen">
                <h5 class="text-white text-lg mb-6">Finance</h5>
                <ul class="space-y-4">
                    <li>
                        <Link :href="route('dashboard')" class="flex items-center text-gray-300 hover:text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 10h11M9 21V3M21 16v5M16 16v5m5-5h-5m5-5v5" />
                        </svg>
                        Dashboard</Link>
                    </li>
                    <li>
                        <Link :href="route('blank')" class="flex items-center pl-7 text-gray-300 hover:text-white">

                        Blank Page</Link>
                    </li>

                    <li>
                        <Link :href="route('logout')" method="post"
                            class="flex items-center text-gray-300 hover:text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H3" />
                        </svg>
                        Logout</Link>
                    </li>
                </ul>
            </nav>

            <!-- Mobile Navbar -->
            <div class="w-full lg:hidden bg-black p-4 flex items-center justify-between">
                <button id="openSidebar" class="text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16m-7 6h7" />
                    </svg>
                </button>
                <div>
                    <i class="text-white">Finance</i>
                </div>
            </div>

            <!-- Main Content -->
            <div class="flex-1 p-6">
                <div class="flex justify-between items-center mb-2">
                    <div class="text-lg">Dashboard</div>
                    <div class="space-x-4">
                        <i class="text-white">
                            <Link :href="route('profile.edit')"> 👤 {{ $page.props.auth.user.name }} </Link>
                        </i>
                    </div>
                </div>
                <div>
                    <!-- Content -->
                    <div class="container-fluid min-h-screen">
                        <div class="bg-[#1E1E1E]">
                            <slot />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
