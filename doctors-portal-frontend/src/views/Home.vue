<template>
  <n-layout class="landing-page">
    <!-- Header -->
    <HeaderComponent />

    <!-- Hero Section -->
    <n-layout-content class="hero-section">
      <div class="hero-container">
        <n-space vertical justify="center" align="center">
          <!-- Logo -->
          <img
            src="../assets/logo.png"
            alt="Deliverit Logo"
            class="logo-img"
            style="width: 200px;"
          />

          <!-- Headline -->
          <n-h2 class="welcome-text">{{ $t('home.welcome') }}</n-h2>
          <n-p class="subtitle">{{ $t('home.subtitle') }}</n-p>

          <!-- Login Button -->
          <n-button type="primary" class="login-button" @click="navigateToLogin">
            {{ $t('home.login') }}
            <n-icon class="ml-2">
              <ArrowForwardSharp />
            </n-icon>
          </n-button>

          <!-- Video Section -->
          <div class="video-wrapper">
            <video
              class="intro-video"
              controls
              playsinline
              poster="../assets/dashboard-pic.png"
            >
              <source src="../assets/intro_video.mp4" type="video/mp4" />
              Your browser does not support the video tag.
            </video>
          </div>

          

          <!-- Features -->
                      <n-h2 class="welcome-text">{{ $t('home.toolsTitle') }}</n-h2>
                      <n-p class="subtitle">{{ $t('home.toolsDescription') }}</n-p>
          <n-space justify="center" wrap class="features-section">

            <n-card hoverable class="feature-card">
              <div class="icon-wrapper">
                <n-icon size="32" color="#2080f0">
                  <check-icon />
                </n-icon>
              </div>
              <n-h3 class="feature-title">{{ $t('home.features.statusTitle') }}</n-h3>
              <n-p class="feature-description">
                {{ $t('home.features.statusDesc') }}
              </n-p>
            </n-card>

            <n-card hoverable class="feature-card">
              <div class="icon-wrapper">
                <n-icon size="32" color="#2080f0">
                  <calendar-icon />
                </n-icon>
              </div>
              <n-h3 class="feature-title">{{ $t('home.features.referralTitle') }}</n-h3>
              <n-p class="feature-description">
                {{ $t('home.features.referralDesc') }}
              </n-p>
            </n-card>

            <n-card hoverable class="feature-card">
              <div class="icon-wrapper">
                <n-icon size="32" color="#2080f0">
                  <patient-icon />
                </n-icon>
              </div>
              <n-h3 class="feature-title">{{ $t('home.features.patientTitle') }}</n-h3>
              <n-p class="feature-description">
                {{ $t('home.features.patientDesc') }}
              </n-p>
            </n-card>
          </n-space>

        </n-space>
      </div>


      <!-- FAQ Section -->
<section class="faq-section">
  <div class="faq-header">
    <h2>{{ $t('home.faq.description') }}</h2>
    <p>
     {{ $t('home.faq.title') }}
    </p>
    <div class="faq-buttons">
      <n-button round type="primary" secondary>
        <n-icon class="mr-1"><MailOutline /></n-icon>{{ $t('home.faq.email') }}
      </n-button>
    </div>
  </div>

  <div class="faq-columns">
    <div class="faq-category">
      <div class="faq-title">
        <n-icon size="20" color="#04d666"><HelpCircleOutline /></n-icon>
        <strong>{{ $t('home.faq.general') }}</strong>
      </div>
  <n-collapse accordion>
   <n-collapse-item :title="$t('home.faq.q1')" name="1">
      {{ $t('home.faq.a1') }}
    </n-collapse-item>

    <n-collapse-item :title="$t('home.faq.q2')" name="2">
      {{ $t('home.faq.a2') }}
    </n-collapse-item>

    <n-collapse-item :title="$t('home.faq.q3')" name="3">
      {{ $t('home.faq.a3') }}
    </n-collapse-item>


    <n-collapse-item :title="$t('home.faq.q4')" name="4">
      {{ $t('home.faq.a4') }}
    </n-collapse-item>

    <n-collapse-item :title="$t('home.faq.q5')" name="5">
      {{ $t('home.faq.a5') }}
    </n-collapse-item>

    <n-collapse-item :title="$t('home.faq.q6')" name="6">
      {{ $t('home.faq.a6') }}
    </n-collapse-item>

    <n-collapse-item :title="$t('home.faq.q7')" name="7">
      {{ $t('home.faq.a7') }}
    </n-collapse-item>
  </n-collapse>
    </div>

   <div class="faq-category">
  <div class="faq-title">
    <n-icon size="20" color="#b53339"><ShieldOutline /></n-icon>
    <strong>{{ $t('home.faq.privacy') }}</strong>
  </div>
  <n-collapse accordion>
   <n-collapse-item :title="$t('home.faq.q8')" name="1">
      {{ $t('home.faq.a8') }}
    </n-collapse-item>

    <n-collapse-item :title="$t('home.faq.q9')" name="2">
      {{ $t('home.faq.a9') }}
    </n-collapse-item>

    <n-collapse-item :title="$t('home.faq.q10')" name="3">
      {{ $t('home.faq.a10') }}
    </n-collapse-item>

     <n-collapse-item :title="$t('home.faq.q11')" name="4">
      {{ $t('home.faq.a11') }}
    </n-collapse-item>
  </n-collapse>
</div>

  </div>
</section>


    </n-layout-content>



    <!-- Footer -->
    <FooterComponent />

    <!-- Contact Modal -->
    <n-modal v-model:show="showContact">
      <n-card style="width: 600px" :title="$t('home.faq.title')">
        <n-p>Email: doctors@deliverit.health</n-p>
        <n-p>{{ $t('home.faq.phone') }}</n-p>
      </n-card>
    </n-modal>
  </n-layout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import {
  NLayout,
  NLayoutContent,
  NModal,
  NCard,
  NIcon,
  NSpace,
  NH2,
  NP,
  NButton
} from 'naive-ui'


import {
  HelpCircleOutline,
  ShieldOutline,
  MailOutline,
  LogoTwitter
} from '@vicons/ionicons5'


import {
  CheckmarkCircleOutline as CheckIcon,
  CalendarOutline as CalendarIcon,
  PersonCircleOutline as PatientIcon,
  ArrowForwardSharp
} from '@vicons/ionicons5'

import HeaderComponent from '@/components/HeaderComponent.vue'
import FooterComponent from '@/components/FooterComponent.vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const router = useRouter()
const showContact = ref(false)
const showFirst = ref(true)

onMounted(() => {
  setInterval(() => {
    showFirst.value = !showFirst.value
  }, 5000)
})

const navigateToLogin = () => {
  router.push('/login')
}
</script>

<style scoped>
html,
body,
#app {
  height: 100%;
  margin: 0;
}



.hero-section {
  flex: 1;
  display: flex;
  justify-content: center;
  align-items: center;
  width: 100%;
}

.landing-page {
  height: 100%;
}

.logo-img {
  width: 100px;
  object-fit: cover;
}

.hero-container {
  padding: 40px 24px;
  height: 100%;
   background-image: url("../assets/bg-pattern.png");
  background-repeat: no-repeat;
  background-size: cover;
}

div.n-layout-scroll-container {
  display: block !important;
}

.welcome-text {
  font-size: 2.5rem;
  text-align: center;
}

.subtitle {
  font-size: 1.2rem;
  font-weight: 300;
  color: #878a99;
  max-width: 700px;
  margin-bottom: 12px;
  text-align: center;
}

.features-section {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 40px;
  margin: 60px auto;
  max-width: 1200px;
}

.feature-card {
  width: 320px;
  height: 100%;
  text-align: center;
  padding: 30px 20px;
  border-radius: 12px;
  border: 1px solid #d4d4d4;
  transition: all 0.3s ease;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
}

.feature-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08);
}

.icon-wrapper {
  width: 60px;
  height: 60px;
  background-color: #e6f4ff;
  border-radius: 50%;
  display: flex;
  justify-content: center;
  align-items: center;
  margin-bottom: 20px;
  justify-self: center;
}

.feature-title {
  font-size: 1.1rem;
  font-weight: 600;
  color: #1a1a1a;
  margin-bottom: 10px;
}

.feature-description {
  font-size: 14px;
  color: #666;
  margin-bottom: 16px;
}

.ml-2 {
  margin-left: 10px;
}

.login-button {
  background-color: #2080f0;
  transition: background-color 0.3s ease;
}

.login-button:hover {
  background-color: #1867c0;
}

.slideshow-wrapper {
  position: relative;
  width: 1300px;
  height: 600px;
  margin: 80px auto;
  overflow: hidden;
}

.transitioned-image {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  object-fit: cover;
}

/* Zoom + Fade Transition */
.fade-zoom-enter-active,
.fade-zoom-leave-active {
  transition: opacity 1s ease, transform 1s ease;
  position: absolute;
  inset: 0;
}

.fade-zoom-enter-from {
  opacity: 0;
  transform: scale(1.05);
}

.fade-zoom-leave-to {
  opacity: 0;
  transform: scale(0.95);
}

.faq-section {
  padding: 100px 20px;
  background-color: #f9fafb;
  text-align: center;
}

.faq-header h2 {
  font-size: 2rem;
  margin-bottom: 12px;
}

.faq-header p {
  max-width: 700px;
  margin: 0 auto 20px;
  color: #888;
}

.faq-buttons {
  display: flex;
  justify-content: center;
  gap: 16px;
  margin-bottom: 60px;
}

.faq-columns {
  display: flex;
  justify-content: center;
  gap: 60px;
  flex-wrap: wrap;
  text-align: left;
}

.faq-category {
  width: 100%;
  max-width: 500px;
}

.faq-title {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 1.1rem;
  margin-bottom: 16px;
}

/* Style for opened item */
:deep(.n-collapse-item.n-collapse-item--active) {
  border-radius: 8px;
  overflow: hidden;
}

/* Optional: highlight active title */
:deep(.n-collapse-item__header.n-collapse-item--active) {
  font-weight: 600;
  color: #1a237e;
  background-color: #f3f5f9;
}

/* Text inside expanded answer */
.faq-answer {
  padding: 12px 20px;
  font-size: 14px;
  color: #777;
  line-height: 1.6;
}

:deep(.n-collapse-item) {
  transition: background-color 0.3s ease;
}


.video-wrapper {
  position: relative;
  width: 100%;
  max-width: 1300px;
  height: 600px;
  margin: 80px auto;
  overflow: hidden;
  border-radius: 12px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

.intro-video {
  width: 100%;
  height: 100%;
  object-fit: cover;
  border-radius: 12px;
}



</style>
