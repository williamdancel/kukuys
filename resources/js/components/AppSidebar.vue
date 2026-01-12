<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import {   
    dashboard, 
    dotaPubTracker, 
    taryahanCs, 
    partnerEnquiries,
    members,
    merchStore,
    adminUsers,       

} from '@/routes';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { LayoutGrid, Gamepad, Gamepad2, Handshake, Users, Store, UserCheck } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },{
        title: 'Dota Pub Tracker',
        href: dotaPubTracker(),
        icon: Gamepad,
    },{
        title: 'Taryahan CS',
        href: taryahanCs(),
        icon: Gamepad2,
    },{
        title: 'Partner Enquiries',
        href: partnerEnquiries(),
        icon: Handshake,
    },{
        title: 'Members',
        href: members(),
        icon: Users,
    },{
        title: 'Merch Store',
        href: merchStore(),
        icon: Store,
    },{
        title: 'Admin Users',
        href: adminUsers(),
        icon: UserCheck,
    }
];

const footerNavItems: NavItem[] = [
    
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
