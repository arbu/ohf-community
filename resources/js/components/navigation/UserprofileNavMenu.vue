<template>
    <div class="position-relative d-none d-md-inline-block">
        <button
            class="btn btn-link text-light px-3"
            @click="toggleMenu()"
        >
            <img
                :src="avatarImage"
                alt="Gravatar"
                class="bg-white rounded-circle"
                style="width: 30px; height: 30px;"
            >
        </button>
        <transition name="fade">
            <ul
                v-if="open"
                class="context-nav userprofile-nav"
            >
                <userprofile-nav-menu-item
                    :url="userprofileUrl"
                    icon="user"
                    :caption="$t('userprofile.profile')"
                />
                <userprofile-nav-menu-item
                    url="javascript:;"
                    icon="sign-out-alt"
                    :caption="$t('app.logout')"
                    @click="logout"
                />
            </ul>
        </transition>
    </div>
</template>

<script>
import UserprofileNavMenuItem from './UserprofileNavMenuItem'
import overlayMenuMixin from './overlayMenuMixin'
export default {
    components: {
        UserprofileNavMenuItem
    },
    mixins: [
        overlayMenuMixin
    ],
    props: {
        avatarImage: {
            type: String,
            required: false,
            default: null
        },
        userprofileUrl: {
            type: String,
            required: false,
            default: null
        },
        logoutUrl: {
            type: String,
            required: true
        }
    },
    methods: {
        logout() {
            postRequest(this.logoutUrl, {});
        }
    }
}
</script>

<style scoped>
.context-nav.userprofile-nav {
    top: 50px;
    box-shadow: none;
    right: -15px;
}
</style>
