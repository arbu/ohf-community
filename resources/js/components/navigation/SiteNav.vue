<template>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark justify-content-between row m-0 px-0">

        <template v-if="authorized">
            <div class="col-auto d-block d-md-none pr-1 pr-sm-3">
                <!-- Back button -->
                <a
                    v-if="backUrl"
                    :href="backUrl"
                    class="btn btn-link text-light"
                >
                    <font-awesome-icon icon="arrow-left" />
                </a>
                <!-- Sidebar navigation toggle -->
                <a
                    v-else
                    href="javascript:;"
                    class="btn btn-link text-light toggle-button"
                    @click="toggleDrawer"
                >
                    <font-awesome-icon icon="bars" />
                </a>
            </div>

            <a
                href="javascript:;"
                class="btn btn-link text-light toggle-button d-none d-md-inline-block ml-3"
                @click="toggleDrawer"
            >
                <font-awesome-icon icon="bars" />
            </a>
        </template>

        <!-- Brand -->
        <div class="col-auto @auth px-0 px-sm-3 @endauth">

            <!-- Logo, Name -->
            <a
                class="navbar-brand d-none d-md-inline-block"
                :href="homeUrl"
            >
                <img
                    v-if="signetUrl"
                    :src="signetUrl"
                />
                {{ appName }}
            </a>

            <!-- Title -->
            <span
                v-if="title"
                class="text-light ml-md-4"
            >{{ title }}</span>

        </div>

        <!-- Right side -->
        <div class="col text-right">

            <!-- Buttons -->
            <site-nav-button
                v-for="(button, key) in buttons"
                :key="key"
                :button="button"
                :name="key"
            />

            <!-- Context menu -->
            <site-nav-menu
                v-if="menu && Object.keys(menu).length > 0"
                :items="menu"
            />

            <template v-if="authorized">
                <div class="position-relative d-none d-md-inline-block">
                    <button class="context-nav-toggle btn btn-link text-light px-3">
                        <img
                            :src="avatarImage"
                            alt="Gravatar"
                            class="bg-white rounded-circle"
                            style="width: 30px; height: 30px;"
                        >
                    </button>
                    <ul class="context-nav userprofile-nav">
                        <li>
                            <a
                                :href="userprofileUrl"
                                class="btn btn-dark btn-block"
                            >
                                <font-awesome-icon icon="user" class="mr-1"/>
                                {{ $t('userprofile.profile') }}
                            </a>
                        </li>
                        <li>
                            <a
                                href="javascript:;"
                                class="btn btn-dark btn-block"
                                @click="logout"
                            >
                                <font-awesome-icon icon="sign-out-alt" class="mr-1"/>
                                {{ $t('app.logout') }}
                            </a>
                        </li>
                    </ul>
                </div>
            </template>
            <template v-else>
                <a :href="loginUrl" class="btn btn-secondary d-none d-md-inline-block">
                    <font-awesome-icon icon="sign-in-alt" /> {{ $t('app.login') }}
                </a>
                <a :href="loginUrl" class="btn text-light d-md-none">
                    <font-awesome-icon icon="sign-in-alt" />
                </a>
            </template>

        </div>

    </nav>

</template>

<script>
import SiteNavButton from './SiteNavButton'
import SiteNavMenu from './SiteNavMenu'
export default {
    components: {
        SiteNavButton,
        SiteNavMenu
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
        }
    },
    methods: {
        logout() {
            postRequest(this.logoutUrl, {});
        },
        toggleDrawer() {
            this.$emit('toggleDrawer')
        }
    }
}
</script>