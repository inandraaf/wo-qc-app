<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <!-- Role-specific Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    @auth
                        {{-- PPIC: Work Orders menu --}}
                        @if(in_array(auth()->user()->role, ['ppic', 'super_admin']))
                            <x-nav-link :href="route('work-orders.index')" :active="request()->routeIs('work-orders.*')">
                                {{ __('Work Orders') }}
                            </x-nav-link>
                        @endif

                        {{-- Operator: Productions menu --}}
                        @if(in_array(auth()->user()->role, ['operator', 'super_admin']))
                            <x-nav-link :href="route('productions.index')" :active="request()->routeIs('productions.*')">
                                {{ __('Productions') }}
                            </x-nav-link>
                        @endif

                        {{-- QC: Quality Control menu --}}
                        @if(in_array(auth()->user()->role, ['qc', 'super_admin']))
                            <x-nav-link :href="route('quality-controls.index')" :active="request()->routeIs('quality-controls.*')">
                                {{ __('Quality Control') }}
                            </x-nav-link>
                        @endif

                        {{-- Manager: View-only access --}}
                        @if(in_array(auth()->user()->role, ['manager']))
                            <x-nav-link :href="route('work-orders.view')" :active="request()->routeIs('work-orders.view')">
                                {{ __('Work Orders') }}
                            </x-nav-link>
                            <x-nav-link :href="route('productions.view')" :active="request()->routeIs('productions.view')">
                                {{ __('Productions') }}
                            </x-nav-link>
                            <x-nav-link :href="route('quality-controls.view')" :active="request()->routeIs('quality-controls.view')">
                                {{ __('Quality Control') }}
                            </x-nav-link>
                        @endif
                    @endauth
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <!-- Role Badge -->
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mr-3"
                      style="background: @auth @switch(auth()->user()->role)
                          @case('super_admin') #7c3aed; color: white; @break
                          @case('ppic') #2563eb; color: white; @break
                          @case('operator') #059669; color: white; @break
                          @case('qc') #d97706; color: white; @break
                          @case('manager') #dc2626; color: white; @break
                          @default #6b7280; color: white; @endswitch @endauth">
                    @auth
                        {{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }}
                    @endauth
                </span>

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            @auth
                @if(in_array(auth()->user()->role, ['ppic', 'super_admin']))
                    <x-responsive-nav-link :href="route('work-orders.index')" :active="request()->routeIs('work-orders.*')">
                        {{ __('Work Orders') }}
                    </x-responsive-nav-link>
                @endif
                @if(in_array(auth()->user()->role, ['operator', 'super_admin']))
                    <x-responsive-nav-link :href="route('productions.index')" :active="request()->routeIs('productions.*')">
                        {{ __('Productions') }}
                    </x-responsive-nav-link>
                @endif
                @if(in_array(auth()->user()->role, ['qc', 'super_admin']))
                    <x-responsive-nav-link :href="route('quality-controls.index')" :active="request()->routeIs('quality-controls.*')">
                        {{ __('Quality Control') }}
                    </x-responsive-nav-link>
                @endif
            @endauth
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
