<template>
    <div class="site-wrapper h-100">
    <div class="site-canvas h-100">
        <!-- Side navigation -->
        <app-drawer
            v-if="authorized"
            :signet-url="signetUrl"
            :app-name="appName"
            :userprofile-url="userprofileUrl"
            :user-name="userName"
            :render-time="renderTime"
            :product-name="productName"
            :app-version="appVersion"
            :changelog-url="changelogUrl"
            :product-url="productUrl"
            :logout-url="logoutUrl"
            :avatar-image="avatarImage"
            :nav-items="navItems"
        ></app-drawer>

        <!-- Main -->
        <main class="d-flex flex-column h-100">

            <!-- Site header -->
            <header class="site-header">
                <site-nav
                    :authorized="authorized"
                    :avatar-image="avatarImageHeader"
                    :login-url="logoutUrl"
                    :userprofile-url="userprofileUrl"
                    :title="title"
                    :home-url="homeUrl"
                    :signet-url="signetUrl"
                    :app-name="appName"
                    :back-url="backUrl"
                    :logout-url="loginUrl"
                    :buttons="buttons"
                    :menu="menu"
                ></site-nav>
            </header>

            <!-- Content -->
            <article class="site-content container-fluid" :class="contentPaddingClass" >

                <slot></slot>

                <!-- Floating action button -->
                <action-button
                    v-if="buttons && buttons.action"
                    :url="buttons.action.url"
                    :icon="buttons.action.icon_floating"
                ></action-button>

            </article>

            <div id="overlay" class="position-absolute h-100 w-100"></div>

            <div id="overlay_dark" class="position-absolute h-100 w-100"></div>
        </main>
    </div>
    </div>
</template>

<script>
import AppDrawer from '@/components/navigation/AppDrawer'
import SiteNav from '@/components/navigation/SiteNav'
import ActionButton from '@/components/navigation/ActionButton';
export default {
    components: {
        AppDrawer,
        SiteNav,
        ActionButton
    },
    props: {
        authorized: Boolean,
        loginUrl: {
            type: String,
            required: false,
            default: null
        },
        userprofileUrl: {
            type: String,
            required: false,
            default: null
        },
        avatarImage: {
            type: String,
            required: false,
            default: null
        },
        avatarImageHeader: {
            type: String,
            required: false,
            default: null
        },
        title: {
            type: String,
            required: false,
            default: null
        },
        homeUrl: {
            type: String,
            required: true
        },
        signetUrl: {
            type: String,
            required: false,
            default: null
        },
        appName: {
            type: String,
            required: true
        },
        backUrl: {
            type: String,
            required: false,
            default: null
        },
        logoutUrl: {
            type: String,
            required: true
        },
        buttons: {
            type: Object,
            required: false,
            default: () => {}
        },
        menu: {
            type: Object,
            required: false,
            default: () => {}
        },
        userName: {
            type: String,
            required: true
        },
        renderTime: {
            type: String,
            required: true
        },
        productName: {
            type: String,
            required: true
        },
        appVersion: {
            type: String,
            required: true
        },
        changelogUrl: {
            type: String,
            required: true
        },
        productUrl: {
            type: String,
            required: true
        },
        navItems: {
            type: Array,
            required: true
        },
        contentPaddingClass: {
            type: String,
            required: false,
            default: 'pt-3'
        }
    }
}
</script>