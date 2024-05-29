<script setup lang="ts">
import Login from "@/components/Login.vue";
</script>
<template>
  <div class="admin-login-box">
    <Login />
  </div>
  <Teleport to="body">
    <div class="copyright">
      Powered by <a href="https://codendot.com" target="_blank">Codendot</a>
      {{ new Date().getFullYear() }}
    </div>
  </Teleport>
</template>
<style lang="scss">
@import "@scss/config";

.route-default-login {
  #login {
    flex: auto;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    width: 100%;
    max-width: 400px;

    .admin-login-box {
      position: relative;
      display: flex;
      flex-direction: column;
      gap: 40px;
      width: 100%;
      justify-content: center;
      align-items: center;

      &:before {
        content: "";
        display: block;
        height: 200px;
        width: 100%;
        background: url("#{$imagesPath}/logo.svg") no-repeat center;
        background-size: auto 100%;
      }
    }
  }

  .copyright {
    display: block;
    margin-top: auto;
    margin-bottom: 30px;
    user-select: none;
    height: 40px;
    line-height: 40px;
    color: $gray-5;
    a {
      text-decoration: none;
      color: $gray-7;
      font-weight: bold;
      transition: all 0.25s ease;
      &:hover {
        color: $primary;
      }
    }
  }
}
</style>
