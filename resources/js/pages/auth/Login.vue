<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AuthLayout from '@/layouts/AuthLayout.vue';
import AppLogo from '@/components/AppLogo.vue';
import { getBoolSetting } from '@/lib/settings';
import { KnownSetting } from '@/types/Setting';

defineOptions({
	layout: {
		title: 'Log in to your account',
		description: 'Enter your email and password below to log in',
	},
});

defineProps<{
	status?: string;
}>();
</script>

<template>
	<AuthLayout>
		<Head title="Log in" />

		<div v-if="status" class="mb-4 text-center text-sm font-medium text-green-600">
			{{ status }}
		</div>

		<form class="">
			<form>
				<AppLogo :width="64" />
				<h1 class="h3 mb-3 fw-normal">Log In</h1>
				<div class="form-floating">
					<input
						type="email"
						class="form-control"
						id="login"
						placeholder="name@example.com" />
					<label for="login">Email</label>
				</div>
				<div class="form-floating">
					<input
						type="password"
						class="form-control"
						id="password"
						placeholder="Password" />
					<label for="password">Password</label>
				</div>
				<div class="form-check text-start my-3">
					<input
						class="form-check-input"
						type="checkbox"
						value="remember"
						id="checkDefault" />
					<label class="form-check-label" for="checkDefault"> Remember Me </label>
				</div>
				<button class="btn btn-primary w-100 py-2" type="submit">Sign in</button>

				<div
					v-if="getBoolSetting(KnownSetting.RegisterAllowed)"
					class="text-center text-sm text-muted-foreground">
					Don’t have an account?
					<Link href="#">Register</Link>
				</div>
				<div
					v-if="getBoolSetting(KnownSetting.PasswordResetAllowed)"
					class="text-center text-sm text-muted-foreground">
					Forgot password?
					<Link href="#">Reset</Link>
				</div>

				<p class="mt-5 mb-3 text-body-secondary">
					<!--© 2017—2025-->
				</p>
			</form>
		</form>
	</AuthLayout>

	<!--




	<Form
		v-bind="store.form()"
		:reset-on-success="['password']"
		v-slot="{ errors, processing }"
		class="flex flex-col gap-6">
		<div class="grid gap-6">
			<div class="grid gap-2">
				<Label for="email">Email address</Label>
				<Input
					id="email"
					type="email"
					name="email"
					required
					autofocus
					:tabindex="1"
					autocomplete="email"
					placeholder="email@example.com" />
				<InputError :message="errors.email" />
			</div>

			<div class="grid gap-2">
				<div class="flex items-center justify-between">
					<Label for="password">Password</Label>
					<TextLink
						v-if="canResetPassword"
						:href="request()"
						class="text-sm"
						:tabindex="5">
						Forgot password?
					</TextLink>
				</div>
				<PasswordInput
					id="password"
					name="password"
					required
					:tabindex="2"
					autocomplete="current-password"
					placeholder="Password" />
				<InputError :message="errors.password" />
			</div>

			<div class="flex items-center justify-between">
				<Label for="remember" class="flex items-center space-x-3">
					<Checkbox id="remember" name="remember" :tabindex="3" />
					<span>Remember me</span>
				</Label>
			</div>

			<Button
				type="submit"
				class="mt-4 w-full"
				:tabindex="4"
				:disabled="processing"
				data-test="login-button">
				<Spinner v-if="processing" />
				Log in
			</Button>
		</div>

		<div class="text-center text-sm text-muted-foreground" v-if="canRegister">
			Don't have an account?
			<TextLink :href="register()" :tabindex="5">Sign up</TextLink>
		</div>
	</Form>
	-->
</template>

<style scoped lang="scss">
form {
}

input[type='email'] {
	margin-bottom: -1px;
	border-bottom-right-radius: 0;
	border-bottom-left-radius: 0;
}

input[type='password'] {
	margin-bottom: 10px;
	border-top-left-radius: 0;
	border-top-right-radius: 0;
}
</style>
