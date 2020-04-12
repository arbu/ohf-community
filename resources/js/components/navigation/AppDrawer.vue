<template>
    <nav class="site-navigation bg-light">
        <div class="h-100 d-flex flex-column">
            <header class="side-nav-header">

                <!-- Logo -->
                <div class="px-3 pt-3">
                    <span class="navbar-brand">
                        <img
                            v-if="signetUrl"
                            :src="signetUrl"
                        />
                        {{ appName }}
                    </span>
                </div>

                <!-- Navigation -->
                <ul class="nav flex-column nav-pills my-3 mt-0">
                    <app-drawer-item
                        v-for="(item, idx) in navItems"
                        :key="idx"
                        :item="item"
                    />
                </ul>

            </header>

            <!-- Footer -->
            <footer class="side-nav-footer">

                <hr>
                <div class="text-center">
                    <a :href="userprofileUrl">
                        <img
                            :src="avatarImage"
                            alt="Gravatar"
                            style="width: 80px; height: 80px;"
                        >
                    </a><br>
                    {{ userName }}
                </div>

                <!-- Logout -->
                <div class="px-3 mt-3">
                    <form class="form-inline" :action="logoutUrl" method="POST">
                        <csrf-field />
                        <button type="submit" class="btn btn-block btn-secondary">
                            <font-awesome-icon icon="sign-out-alt" />
                            {{ $t('app.logout') }}
                        </button>
                    </form>
                </div>

                <hr>
                <p class="copyright text-muted px-3">
                    <a
                        :href="productUrl"
                        target="_blank"
                        class="text-dark"
                    >{{ productName }}</a>
                    <a :href="changelogUrl">{{ appVersion }}</a><br>
                    &copy; Nicolas Perrenoud<br>
                    Page rendered in {{ renderTime }} ms
                </p>
            </footer>
        </div>
    </nav>
</template>

<script>
import CsrfField from '@/components/CsrfField'
import AppDrawerItem from './AppDrawerItem'
export default {
    components: {
        CsrfField,
        AppDrawerItem
    },
    props: {
        signetUrl: {
            type: String,
            required: false,
            default: null
        },
        appName: {
            type: String,
            required: true
        },
        userprofileUrl: {
            type: String,
            required: false,
            default: null
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
        logoutUrl: {
            type: String,
            required: true
        },
        avatarImage: {
            type: String,
            required: false,
            default: null
        },
        navItems: {
            type: Array,
            required: true
        }
    }
}
</script>

<style scoped>
.site-navigation {
    width: 230px;
    height: 100%;
    position: absolute;
    top: 0;
    left: -230px;
    overflow-y: auto;
}

.side-nav-header {
    flex: 1;
}

.side-nav-footer {
    flex: 0 0 auto;
}

.side-nav-footer .copyright {
    font-size: 11px;
    line-height: 12px;
}
</style>