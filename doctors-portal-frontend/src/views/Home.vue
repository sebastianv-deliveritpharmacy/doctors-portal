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
            src="https://deliveritpharmacy.com/wp-content/uploads/2024/04/Logo-Resurrgaction-B-Vertical.png"
            alt="Deliverit Logo"
            class="logo-img"
            style="width: 200px;"
          />

          <!-- Headline -->
          <n-h2 class="welcome-text">Welcome to DeliverIt Doctor's Portal</n-h2>
          <n-p class="subtitle">
            Streamline prescription management and referral tracking with real-time integration.
          </n-p>

          <!-- Login Button -->
          <n-button type="primary" class="login-button" @click="navigateToLogin">
            Doctor Login
            <n-icon class="ml-2">
              <ArrowForwardSharp />
            </n-icon>
          </n-button>

          <!-- Slideshow -->
          <div class="slideshow-wrapper">
                <Transition
                  mode="in-out"
                  enter-active-class="fade-zoom-enter-active"
                  leave-active-class="fade-zoom-leave-active"
                  enter-from-class="fade-zoom-enter-from"
                  leave-to-class="fade-zoom-leave-to"
                >
                  <img
                    v-if="showFirst"
                    key="image-1"
                    src="./../assets/doctor-porta.png"
                    class="transitioned-image"
                  />
                  <img
                    v-else
                    key="image-2"
                    src="./../assets/admin-portal.png"
                    class="transitioned-image"
                  />
                </Transition>

          </div>
          

          <!-- Features -->
                      <n-h2 class="welcome-text">Powerful Tools for Smarter Care</n-h2>
                      <n-p class="subtitle">
            Monitor prescriptions, referrals, and patient statuses in real time — all from one intuitive, doctor-focused platform.
          </n-p>
          <n-space justify="center" wrap class="features-section">

            <n-card hoverable class="feature-card">
              <div class="icon-wrapper">
                <n-icon size="32" color="#2080f0">
                  <check-icon />
                </n-icon>
              </div>
              <n-h3 class="feature-title">Prescription Status</n-h3>
              <n-p class="feature-description">
                View and manage prescriptions marked as Active, Inactive, or Processing — updated in real-time via CaredAPI.
              </n-p>
            </n-card>

            <n-card hoverable class="feature-card">
              <div class="icon-wrapper">
                <n-icon size="32" color="#2080f0">
                  <calendar-icon />
                </n-icon>
              </div>
              <n-h3 class="feature-title">Referral Workflow</n-h3>
              <n-p class="feature-description">
                Stay informed on each referral’s journey — from Benefits Verification to Insurance Approval or Denial.
              </n-p>
            </n-card>

            <n-card hoverable class="feature-card">
              <div class="icon-wrapper">
                <n-icon size="32" color="#2080f0">
                  <patient-icon />
                </n-icon>
              </div>
              <n-h3 class="feature-title">Patient Status</n-h3>
              <n-p class="feature-description">
                Track patient activation manually or programmatically. Ensure visibility into each patient’s current state.
              </n-p>
            </n-card>
          </n-space>

        </n-space>
      </div>


      <!-- FAQ Section -->
<section class="faq-section">
  <div class="faq-header">
    <h2>Frequently Asked Questions</h2>
    <p>
      If you cannot find an answer to your question in our FAQ, you can always contact us or email us.
      We will answer you shortly!
    </p>
    <div class="faq-buttons">
      <n-button round type="primary" secondary>
        <n-icon class="mr-1"><MailOutline /></n-icon>Email Us
      </n-button>
    </div>
  </div>

  <div class="faq-columns">
    <div class="faq-category">
      <div class="faq-title">
        <n-icon size="20" color="#04d666"><HelpCircleOutline /></n-icon>
        <strong>General Questions</strong>
      </div>
  <n-collapse accordion>
    <n-collapse-item title="How can I track the status of prescriptions in real time?" name="1">
      Our platform syncs directly with CaredAPI to give you real-time updates on every prescription. You can see whether a prescription is Active, Inactive, or currently Processing — all from your dashboard.
    </n-collapse-item>

    <n-collapse-item title="What stages are included in the referral workflow?" name="2">
      The referral workflow includes Benefits Verification, Insurance Approval, Denial, and other key steps. You can follow each referral's journey without having to call or email external parties.
    </n-collapse-item>

    <n-collapse-item title="Can I see whether a patient has been activated?" name="3">
      Yes. You can monitor patient activation manually or programmatically through the portal. Each patient’s current state is clearly labeled and searchable.
    </n-collapse-item>

    <n-collapse-item title="How does the system integrate with CaredAPI for prescription updates?" name="4">
      Our integration with CaredAPI pulls prescription updates in real time. This ensures your records are always current without requiring manual syncing or third-party tools.
    </n-collapse-item>

    <n-collapse-item title="Is it possible to monitor multiple patient statuses from one dashboard?" name="5">
      Absolutely. Our doctor-focused dashboard gives you an overview of all patient statuses at once. This includes their prescription states, referral progress, and activation status.
    </n-collapse-item>

    <n-collapse-item title="What types of prescription states are supported?" name="6">
      We currently support Active, Inactive, and Processing states — with more granular tracking coming soon. Each status is color-coded and easily filterable.
    </n-collapse-item>

    <n-collapse-item title="How frequently is patient data updated in the portal?" name="7">
      Patient data is updated in real time as events occur. You never have to wonder if you're looking at outdated information — what you see is always the current state.
    </n-collapse-item>
  </n-collapse>
    </div>

   <div class="faq-category">
  <div class="faq-title">
    <n-icon size="20" color="#b53339"><ShieldOutline /></n-icon>
    <strong>Privacy & Security</strong>
  </div>
  <n-collapse accordion>
    <n-collapse-item title="Is the platform HIPAA compliant?" name="1">
      Yes. Our platform is fully HIPAA compliant. We adhere to strict data protection and privacy standards to ensure that all patient and prescription information is secure and confidential.
    </n-collapse-item>

    <n-collapse-item title="How is patient data protected?" name="2">
      Patient data is encrypted both in transit and at rest. We use secure APIs, access control, and logging to ensure only authorized users can view or manage sensitive information.
    </n-collapse-item>

    <n-collapse-item title="Who can access medical information on the platform?" name="3">
      Only verified and authorized users such as licensed physicians or approved medical staff can access patient or prescription data. Role-based permissions control visibility at every level.
    </n-collapse-item>

    <n-collapse-item title="Do you store or process any data outside the U.S.?" name="4">
      No. All data is stored and processed within secure U.S.-based data centers that comply with HIPAA and other healthcare industry regulations.
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
      <n-card style="width: 600px" title="Contact Us">
        <n-p>Email: doctors@deliverit.health</n-p>
        <n-p>Phone: (555) 123-4567</n-p>
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



</style>
