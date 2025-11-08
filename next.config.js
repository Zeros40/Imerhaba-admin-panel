/** @type {import('next').NextConfig} */
const nextConfig = {
  reactStrictMode: true,
  images: {
    domains: ['firebasestorage.googleapis.com'],
  },
  i18n: {
    locales: ['en', 'ar', 'bs'],
    defaultLocale: 'en',
  },
}

module.exports = nextConfig
