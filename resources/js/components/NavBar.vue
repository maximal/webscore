<script setup lang="ts">
import AppLogo from '@/components/AppLogo.vue';
import DropdownLink from '@/components/DropdownLink.vue';
import { APP_NAME } from '@/config';
import { getBoolSetting, getStringSetting } from '@/lib/settings';
import { KnownSetting } from '@/types/Setting';
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import { login } from '@/routes';
import { newMethod } from '@/routes/score';

const isLocal = ref(typeof window !== 'undefined' && window.location.host === 'localhost');
</script>

<template>
	<!-- Navbar -->
	<nav class="navbar sticky-top_ navbar-expand-sm border-bottom">
		<div class="container-fluid">
			<a href="/" class="navbar-brand">
				<AppLogo height="32" class="me-1" />
				{{ getStringSetting(KnownSetting.AppName, APP_NAME) }}
				<sup v-if="isLocal">
					<small class="text-info d-inline d-sm-none">XS</small>
					<small class="text-info d-none d-sm-inline d-md-none">SM</small>
					<small class="text-info d-none d-md-inline d-lg-none">MD</small>
					<small class="text-info d-none d-lg-inline d-xl-none">LG</small>
					<small class="text-info d-none d-xl-inline d-xxl-none">XL</small>
					<small class="text-info d-none d-xxl-inline">XXL</small>
				</sup>
			</a>

			<template v-if="$page.props.auth.user">
				<button
					class="navbar-toggler"
					type="button"
					data-bs-toggle="collapse"
					data-bs-target="#navbarDropdown"
					aria-controls="navbarDropdown"
					aria-expanded="false"
					aria-label="Toggle navigation">
					<span class="navbar-toggler-icon" />
				</button>
				<div id="navbarDropdown" class="collapse navbar-collapse">
					<ul class="navbar-nav w-100 justify-content-end">
						<!--
						<li class="nav-item"><NavLink to="balance">Баланс</NavLink></li>
						<li class="nav-item"><NavLink to="services">Услуги</NavLink></li>
						-->
						<li class="nav-item">
							<RouteLink to="dashboard" class="nav-link">New Score</RouteLink>
						</li>
						<li class="nav-item dropdown">
							<button
								class="nav-link dropdown-toggle"
								data-bs-toggle="dropdown"
								aria-expanded="false">
								{{ $page.props.auth.user.name }}
							</button>
							<ul class="dropdown-menu">
								<li v-if="$page.props.auth.user.role === 'admin'">
									<RouteLink to="admin" class="dropdown-item">Admin</RouteLink>
								</li>
								<li>
									<RouteLink to="my.scores" class="dropdown-item">
										My Scores
									</RouteLink>
								</li>
								<li>
									<RouteLink to="profile.edit" class="dropdown-item">
										Profile
									</RouteLink>
								</li>
								<li class="dropdown-divider"></li>
								<li>
									<DropdownLink to="logout" method="post" as="button" class="dropdown-item">
										Log Out
									</DropdownLink>
								</li>
							</ul>
						</li>
					</ul>
				</div>
			</template>
			<template v-else>
				<div id="navbarDropdown" class="collapse navbar-collapse">
					<ul class="navbar-nav w-100 justify-content-end">
						<li v-if="getBoolSetting(KnownSetting.GuestUploadAllowed)" class="nav-item">
							<Link :href="newMethod().url" class="nav-link">
								<i class="bi bi-plus-lg" />
								Upload
							</Link>
						</li>
						<li v-if="getBoolSetting(KnownSetting.LoginAllowed)" class="nav-item">
							<Link :href="login().url" class="nav-link">Log In</Link>
						</li>
					</ul>
				</div>
			</template>
		</div>
	</nav>
</template>

<style scoped lang="scss">
.navbar-brand {
	&:hover {
		svg {
			color: var(--bs-link-hover-color);
		}
	}
}
.dropdown-toggle {
	padding-top: 7px;
}
</style>
