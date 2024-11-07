import React from 'react';
// Import your image
import myImage from '../assets/portrait.jpeg'
import { useTranslation } from 'react-i18next';

const About = () => {
    const { t } = useTranslation();

    return (
        <div name="about" className="w-full min-h-screen bg-[#0a192f] text-gray-300">
            <div className="max-w-[1000px] mx-auto p-4 flex flex-col justify-center w-full h-full">
                <div className="pb-8">
                    <p className="text-4xl font-bold inline border-b-4 border-pink-600">{t("about-header")}</p>
                </div>
                <div className="flex flex-col md:flex-row items-center">
                    {/* Image Section */}
                    <div className="md:w-1/2 mb-4 md:mb-0">
                        <img src={myImage} alt="Chen Kaminski" className="rounded-lg shadow-lg" />
                    </div>
                    {/* Text Section */}
                    <div className="md:w-1/2 md:pl-8">
                        <h2 className="text-3xl font-bold mb-4">{t("About-hi")}</h2>
                        <p className="mb-4">
                            As a versatile full stack developer, I specialize in creating innovative digital solutions, with a focus on the fintech industry. My expertise lies in developing secure and efficient payment systems, crafting both web and native applications, and building comprehensive fintech solutions.
                        </p>
                        <p className="mb-4">
                            I excel in creating robust payment pages, integrating payment solutions, and developing custom scripts to enhance functionality and automate processes. My approach combines technical proficiency with a keen understanding of business needs.
                        </p>
                        <p>
                            Whether it's a complex financial platform or a streamlined payment gateway, I'm committed to delivering high-quality, scalable solutions that drive business growth. I thrive in collaborative environments and am always eager to tackle new challenges in the ever-evolving world of technology and finance.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    )
}

export default About;