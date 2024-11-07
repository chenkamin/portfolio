import React from 'react';
import { useTranslation } from 'react-i18next';

const LanguageSelector = () => {
  const { i18n } = useTranslation();

  const changeLanguage = (event) => {
    const lng = event.target.value;
    i18n.changeLanguage(lng);
    document.documentElement.dir = lng === 'he' ? 'rtl' : 'ltr';
  };

  return (
    <div className="language-selector">
      <select
        onChange={changeLanguage}
        value={i18n.language}
        className="language-dropdown bg-transparent border border-gray-300 text-gray-300 py-1 px-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent"
      >
        <option value="en" className="bg-[#0a192f] text-gray-300">🇺🇸 EN</option>
        <option value="he" className="bg-[#0a192f] text-gray-300">🇮🇱 עב</option>
      </select>
    </div>
  );
};

export default LanguageSelector;