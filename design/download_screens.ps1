# Download all AGU E-News design screens from Google Stitch AI
# Run this from the project root: e:\laragon\www\enews

$screensDir = ".\design\screens"

$screens = @(
    @{ name = "01_homepage"; url = "https://lh3.googleusercontent.com/aida/AOfcidXBd0YSURoG-mJd_Lw_OQY9wJmfX5qFqvVQ7NUIWyOZwCJfEz73R-oamtLJGA-qnKyV2VIxKd7iee1ISFtqXJD0QAKUCozIEgXtwJqmAsHARAQJOwYKYnrAHX0iUWnVKsjlyt9_SNPyLGgMY6CnVc3AwdmqA0FxGXa3UFqwG59_BncdVJjojjzrQsd0pDvg81S4jpteGZ9nGvqZr4nxIPQIH081r6D8ur749tBWSYaYkdUkgssEsKC4wA0" },
    @{ name = "02_article_detail"; url = "https://lh3.googleusercontent.com/aida/AOfcidUt60uX-V6bGKtSSUgKQwNwGSbsIue91E_77fCkwSkcvvYp9eCn4dvbRThE5opYkYqQUb4_Yv9yIJDZGhPUtlIB-DjJkMA9qyPdcBFnvIMdE5ZgRMijLn9EgdTuxxnLNVkG8rMq5svZqOxP40TxK88XmCGKk7KYFu3RABgC2zNS7s5NNpqr2QRQGUJdzMtQNtcLrFNqV1wuBQ4erClAI6UeAtpBsaDf8iMXE_pj0O9EbmOW22ASs8trlxc" },
    @{ name = "03_category_listing"; url = "https://lh3.googleusercontent.com/aida/AOfcidVsAX-Lm5RghMPOPjduq6fRqpYrRG3hsrtx_ZRRFbTkstqD4A8FDIv2IiD-befm2DTOKVHK1i5HghOq74PZ5nyKj636Lb8W2QeBZcR6u1yT5C-ob2YTo9dzmG1C4_-P7TF_nGhuG9Mcmzgy3qClDFSL9C_gap9GkcnubmWsJyqILg4otIgf6HkjYEi3fAwU9gJ2-46ZMIMOTUlSxNQudGaWTEIWPc7h42jyA-PqR_c2ZCcbWfmBIV0_X0Y" },
    @{ name = "04_login_normal"; url = "https://lh3.googleusercontent.com/aida/AOfcidW1AUE2f2FCjHT4itx10Tfj7T0zEh0bABL6SHpAewauJDEp2YMKaeoZU1xHM8HaQNTkz6x-v7f2IOkcoJL5dZ9ut4yO30f9Q9NO7thQXjxTFwNLdI3dDmkM-3ryvfOEm3Z82_dv_HPuDHGP-txSL9AtuCMAYFD1USU2Ka53c7i7HB9e004jGKax-FCwO-Af6MD0gIq2NpgdmEPjJW_ThsLJyQv2JhpD1PsCFjwCvT9Upu3Snp-Nw4nblQY" },
    @{ name = "05_login_error"; url = "https://lh3.googleusercontent.com/aida/AOfcidXugnC1pxV6cvq5qpRnLKNo4iDGf6i3ehxem-IFSkSYk8cZ5okbkXPc3aHKGoAaFz3PlIBb4F1USw116uuvwSEoq-5Xj6D_veCK1sMlYRorPYUdZYf8FZKc4YRBi3F8J-a4LWo6ehoL1B3PEX8waTujcGZLyEm-AfrdOUkFfmBfIojH85tG9N62PaIOhhEc-MyZ-IB-OUmvv4Rl51T0OyNq-iFnjAykLKin_3ls3f7nKUme7x4Aa8Qzdv4" },
    @{ name = "06_search_results"; url = "https://lh3.googleusercontent.com/aida/AOfcidW8Lxk4ZZYQCXclSWwLFMqbC-Y-WK-aO2rm08llHTLcqL8FmbP-su5KprJgzjgFkPYk19ub9i908FnVe3ahFNs0TgLeGO0E1BFd5ysOkbqN94c8EYLnMIPgU3u1qm8jsVI6Eej9IkZ7mhGH6QGvWGM3gcCRksLKArz6JavBdrTU99tF-lUMjGjY5LpsGlsLnZCPZY6WmbGfNTyhUMnmIez1-_hKSifQClCMC8F4hrY5_brGGqHk6akMDg" },
    @{ name = "07_admin_dashboard"; url = "https://lh3.googleusercontent.com/aida/AOfcidVrk2JuPA2gV8SiTGUtvvu4M8FjVzXbAJUlFnfxEnPXoDUNQ802xvHkukZH-4eyYZJ_m-8RMWCDljnZdG80z4vjpUqctaJ3f0Ft8Z-DGv1vdchxGXSUbrZxB26gLcpyGajW8vEJrfgDC28vtFzZz8b56RC6LNDFB2ytCJ_vMNoHQCInLcNmaMHTxwtemKvQqPxctNGi0kPa4PfTOvKYaQbdfgPlBDn2_IDD_YduSg3_yhgKQTVAr4zu7tg" },
    @{ name = "08_article_management"; url = "https://lh3.googleusercontent.com/aida/AOfcidU_GGy-WdHMecPCPBODR3kz8pOzyZ4psRiFlkQGIy_U0njKSFozFm4UJ5yvnnsUgymoIiqNa5R_6IXglH86PlrBEROhCyzW1rMBNwZuNFedjVQ7nVhDle0uVv_NoqeT0O-tC6xvznss_3FCAr_BsQGBytThpUJc-RPctYKl68JBASHL6_KFktjTZwJqPfjwzaLGFI1lNX4WuZB1V22Zr0KYkO0KgimMfo5RYfo9DFB5M9K0jl0zM-11uxI" },
    @{ name = "09_article_editor"; url = "https://lh3.googleusercontent.com/aida/AOfcidVyeEvleaQu9Tcxn3nKJ0xs--a_p1aE60M19qT_7O2gCpEyub9m8YX5uSAYUZt6qH9VZ1m2jXf59j-Mc4Bd4sdUW2loIxFewEbbqQFpWTM6kvJrWfwKXJJO1OGgcdsj4uHUrxH9cGKVojN6fOamldcpcvDNYz2uXX8QCO33ldBcVEpZ-d3toOEc14jpd7qskqd92yGFN_iniyTNKFf0YJ1yKZSBpAitfonThDTvpXkELqy_CUCYy69EPdc" },
    @{ name = "10_user_management"; url = "https://lh3.googleusercontent.com/aida/AOfcidUGxDC-c2dLxLn9Fyvy8mfPQG9oDzsptne_hYcdR_bBfKg7ItxrpZwtu7sTpza2GwcS34vhh1PNIBH5AgflY_X1fJRWTWX3nys37Bu7WQ_75U8r2uLZvLWdf8zUWuagMVQpuQXPUWjDYpSOcS7kbF46FkS3dgqOA9hXd5n0KppdryyIH0xmGf42d0brA9K3dqUt_cUIUVIj41qGPtmIBm4Cr1mpLrSMiXU-Qz7QRKbvlBm1G1cX6EEK550" },
    @{ name = "11_categories_comments_media"; url = "https://lh3.googleusercontent.com/aida/AOfcidUaGQtPnSYl2QSCb8vY3eeU2NbVYrV9F8YBKbuSTumcOckp_CSs1jhCPzuTLgfso_oIICWzJ7f8o2D7ron2TGxtDtLFBoH3yWkZleSrEXfd7Oo6gVkkHcHwEBaKwabvPyov788mezgX58ZO7-EN2oUnIzfQh4F02Ppg5H5F02dOI5zIqP-eYm3RwI3wb2v28xhcAw_p1wTKBgiv_V5H1hIZJU4c5YIW7kUY0J0Bt83cAHR6rAeQCOMHrBY" },
    @{ name = "12_reports_analytics"; url = "https://lh3.googleusercontent.com/aida/AOfcidUl5WsUS2iNiQgYCuyw5zewAcwTUBxGO9Xm-R3fn7B3o1K2X8LtXwWD0tOeZoFZIksdTyjUgt5MVBa5WgI7QVk3iUHozD6wPqORkhTeFVrd4GBwduewvMlXAFRFbzs3ttNhYIkCgUJsPX4RQnSBqf_yhPNillNH2BccZ3HOONC8YaI3y6VxT6zBo7TN1KbSd9i_WCYTlkiF_rvSdntuePfWNzwWc622lxqz2AJrJObSmW5iOLV_yl1nNz4" }
)

foreach ($screen in $screens) {
    $filename = "$screensDir\$($screen.name).png"
    Write-Host "Downloading $($screen.name)..."
    try {
        Invoke-WebRequest -Uri $screen.url -OutFile $filename -UseBasicParsing
        Write-Host "  -> Saved: $filename" -ForegroundColor Green
    } catch {
        Write-Host "  -> Failed: $_" -ForegroundColor Red
    }
}

Write-Host "`nDone! All screens downloaded to $screensDir" -ForegroundColor Cyan
