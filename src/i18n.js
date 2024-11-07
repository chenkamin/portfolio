import i18n from 'i18next';
import { initReactI18next } from 'react-i18next';

i18n
  .use(initReactI18next)
  .init({
    resources: {
      en: {
        translation: {
          // English translations
          "home": "Home",
          "about": "About",
          "skills": "Skills",
          "work": "Work",
          "contact": "Contact",
          "Hi":"Hi my name is",
          "name": "Chen Kaminski",
          "home-text": "Crafting efficient, user-friendly web solutions with JavaScript expertise.Experienced in MERN and LAMP stacks. Passionate about clean code, continuous learning,and collaborative problem-solving. I approach each project with a smile and a service-oriented mindset, always aiming to exceed expectations and deliver exceptional results",
          "home-title": "Im A full stack Developer",
          "view-work": "View Work",
          "about-header" : "About",
          "About-hi":"Hi, I'm Chen",
          "about-text" : "As a versatile full stack developer, I specialize in creating innovative digital solutions, with a focus on the fintech industry. My expertise lies in developing secure and efficient payment systems, crafting both web and native applications, and building comprehensive fintech solutions. I excel in creating robust payment pages, integrating payment solutions, and developing custom scripts to enhance functionality and automate processes. My approach combines technical proficiency with a keen understanding of business needs. Whether it's a complex financial platform or a streamlined payment gateway, I'm committed to delivering high-quality, scalable solutions that drive business growth. I thrive in collaborative environments and am always eager to tackle new challenges in the ever-evolving world of technology and finance.",
          "skils-header" : "Skills",
          "skils-list" : "Here's a glimpse of the technologies I work with",

          // Add more translations as needed
        }
      },
      he: {
        translation: {
          // Hebrew translations
          "home": "דף הבית",
          "about": "אודות",
          "skills": "כישורים",
          "work": "עבודות",
          "contact": "צור קשר",
          "Hi": "שלום קוראים לי",
          "name": "חן קמינסקי",
          "home-text" : "מתמחה בלפתח אפליקציות, מערכות, אתרים. מביא ניסיון במגוון שפות פיתוח וסביבות. אוהב ללמוד, להתפתח ולשתף פעולה.מגיע לכל פרויקט עם חיוך ושירותיות כדי לבצע את העבודה הטובה ביותר ולענות על צרכי הלקוח",
          "home-title": "ואני מפתח פולסטאק",
          "view-work": "עבודות שלי",
          "about-header" : "קצת עליי",
          "About-hi":"היי, אני חן",
          "about-text" : "As a versatile full stack developer, I specialize in creating innovative digital solutions, with a focus on the fintech industry. My expertise lies in developing secure and efficient payment systems, crafting both web and native applications, and building comprehensive fintech solutions. I excel in creating robust payment pages, integrating payment solutions, and developing custom scripts to enhance functionality and automate processes. My approach combines technical proficiency with a keen understanding of business needs. Whether it's a complex financial platform or a streamlined payment gateway, I'm committed to delivering high-quality, scalable solutions that drive business growth. I thrive in collaborative environments and am always eager to tackle new challenges in the ever-evolving world of technology and finance.",
          "skils-header" : "טכנולוגיות",
          "skils-list" : "הנה כמה מהטכנולוגיות שעבדתי איתן",



          // Add more translations as needed
        }
      }
    },
    lng: "en", // Set default language to English
    fallbackLng: "en",
    interpolation: {
      escapeValue: false
    }
  });

export default i18n;