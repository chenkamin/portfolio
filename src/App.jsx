import { useState } from 'react'
import { useTranslation } from 'react-i18next';
import './i18n';
import './index.css'
import NavBar from './components/NavBar'
import Home from './components/Home'
import About from './components/About'
import Skils from './components/Skils'
import Work from './components/Work'
import Contant from './components/Contant'

function App() {
  const { i18n } = useTranslation();
  const [language, setLanguage] = useState('en');

  const changeLanguage = (lng) => {
    i18n.changeLanguage(lng);
    setLanguage(lng);
    document.documentElement.dir = lng === 'he' ? 'rtl' : 'ltr';
  };

  return (
    <div className={language === 'he' ? 'rtl' : 'ltr'}>

      <NavBar />
      <Home />
      <About />
      <Skils />
      <Work />
      <Contant />
    </div>
  )
}

export default App