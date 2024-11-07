import React, { useState } from 'react'
import { useTranslation } from 'react-i18next';
import { FaBars, FaTimes, FaGithub, FaLinkedinIn } from 'react-icons/fa'
import { HiOutlineMail } from 'react-icons/hi'
import { BsFillPersonLinesFill } from 'react-icons/bs'
import Logo from '../assets/logo.png'
import { Link } from 'react-scroll'
import LanguageSelector from './LanguageSelector';

const NavBar = () => {
    const [nav, setNav] = useState(false)
    const { t } = useTranslation();

    const handleClick = () => {
        setNav(!nav)
    }

    return (
        <div className="fixed w-full h-[80px] flex justify-between items-center px-4 bg-[#0a192f] text-gray-300">
            <div className="text-pink-600">
                CHEN K
            </div>

            {/* menu */}
            <ul className="hidden md:flex items-center">
                <li>
                    <Link to="home" smooth={true} duration={500}>{t('home')}</Link>
                </li>
                <li>
                    <Link to="about" smooth={true} duration={500}>{t('about')}</Link>
                </li>
                <li>
                    <Link to="skills" smooth={true} duration={500}>{t('skills')}</Link>
                </li>
                <li>
                    <Link to="work" smooth={true} duration={500}>{t('work')}</Link>
                </li>
                <li>
                    <Link to="contant" smooth={true} duration={500}>{t('contact')}</Link>
                </li>
                <li className="ml-4">
                    <LanguageSelector />
                </li>
            </ul>

            {/* Hamburger */}
            <div onClick={handleClick} className="md:hidden z-10">
                {nav ? <FaTimes /> : <FaBars />}
            </div>

            {/* mobile */}
            <ul className={nav ? 'absolute top-0 left-0 w-full h-screen bg-[#0a192f] flex flex-col justify-center items-center' : 'hidden'}>
                <li className="py-6 text-4xl">
                    <Link onClick={handleClick} to="home" smooth={true} duration={500}>{t('home')}</Link>
                </li>
                <li className="py-6 text-4xl">
                    <Link onClick={handleClick} to="about" smooth={true} duration={500}>{t('about')}</Link>
                </li>
                <li className="py-6 text-4xl">
                    <Link onClick={handleClick} to="skills" smooth={true} duration={500}>{t('skills')}</Link>
                </li>
                <li className="py-6 text-4xl">
                    <Link onClick={handleClick} to="work" smooth={true} duration={500}>{t('work')}</Link>
                </li>
                <li className="py-6 text-4xl">
                    <Link onClick={handleClick} to="contant" smooth={true} duration={500}>{t('contact')}</Link>
                </li>
                <li className="py-6">
                    <LanguageSelector />
                </li>
            </ul>

            {/* Social icons */}
            <div className="hidden lg:flex fixed flex-col top-[35%] left-0">
                <ul>
                    <li className="w-[160px] h-[60px] flex justify-between items-center ml-[-100px] hover:ml-[-10px] duration-300 bg-blue-600">
                        <a href="https://www.linkedin.com/in/chen-kaminski/" className="flex justify-between items-center w-full text-gray-300">
                            Linkedin <FaLinkedinIn size={30} />
                        </a>
                    </li>
                    <li className="w-[160px] h-[60px] flex justify-between items-center ml-[-100px] hover:ml-[-10px] duration-300 bg-[#333333]">
                        <a href="https://github.com/chenkamin" className="flex justify-between items-center w-full text-gray-300">
                            Github <FaGithub size={30} />
                        </a>
                    </li>
                    <li className="w-[160px] h-[60px] flex justify-between items-center ml-[-100px] hover:ml-[-10px] duration-300 bg-[#6fc2b0]">
                        <a
                           href="mailto:chenkamin@gmail.com" 
                           rel="noreferrer"
                            className="flex justify-between items-center w-full text-gray-300">
                            Email <HiOutlineMail size={30} />
                        </a>
                    </li>
                    <li className="w-[160px] h-[60px] flex justify-between items-center ml-[-100px] hover:ml-[-10px] duration-300 bg-[#565f69]">
                        <a
                         href="public/resume.pdf" // assuming your PDF is named "resume.pdf" in public folder
                         download
                         className="flex justify-between items-center w-full text-gray-300">
                            {t('resume')} <BsFillPersonLinesFill size={30} />
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    )
}

export default NavBar