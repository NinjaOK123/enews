$screens = @(
    @{ name = "13_error_pages_404_403_500"; url = "https://lh3.googleusercontent.com/aida/AOfcidUj8By4Z_YidMmbNrXDNJROYXopNrWmh09-f4ZVP4E6ud7JpqOcHEk_OrnXwttH039tzuE5v5TAMfOhHJH6X2OhbAH_9tPi9rDJEKxj97sIv843bBKL9t3tcNrs-9EZ5i4VrDWuI6Wh0hQ9yES37swQB4Q4SAJKZiNy_Cd9OsKGIPFtNPwhWUejtQs5GUgGSyddnUp-qmMDLzTz5ZVsaeoFWtuf_J6wlWrDHA8iVwTcHBNGzppAh1OUDQ" },
    @{ name = "14_mobile_homepage"; url = "https://lh3.googleusercontent.com/aida/AOfcidUhAqQSB_p41dbsLjCMS41CRGkvBV_Tx7c96IJQulcOazzV8cwwzFlYzIjbHaJXVXREIAW_kMTnBX8yOKV53gtYxmGh8yQfQ3ejkXdKHBnaHWII3zqDcUYnFwWnEjKruOV6hRMVT7VYfk5Wu2yCW16ECUf1t5GCmskYjlJDk0LwHpSx0h_y1fgN_JI1WOjg092tgGEpmjMMFVQrreOo6StUiZ4fXUhUz4LHzIAZRmQD5PJ2EbTpKJu5Zg" },
    @{ name = "15_mobile_article_detail"; url = "https://lh3.googleusercontent.com/aida/AOfcidVwnQ0aX99nps0bcV4IinXI0RphYpR6te-RX4J9m5RrzfqROV2AusdZeBBpxDtU4pUJkItNOXihW_TZ0qKMcYoXyHIIVUMS4H6iCat_I9b28JmrJlMjlKQ3nsAbGTQKbpaI6oeJJp5FjiGdxVs41lkp3aonKDryI436vSO08UwJkQhdsmwsSAAaxCkvcHpQM84Pt0FYQeoDr7UNRAas9bTwDZmBk6lBWBF-xhdy8mnOvGpi-1Ly8Sy2SxE" },
    @{ name = "16_contributor_dashboard"; url = "https://lh3.googleusercontent.com/aida/AOfcidVyXS2u7DlmClFwhm6qi4odaJZ_XM-KVxRio2dNFxyUATB9g8XZ6s93lvo-Aw3HI5StV75j6glbNeqVHorib1HLdHlyaxa8XRHUEGu6Xj22eHjjVqmW6AVIWbQ58isgxxDBj1YoPifYnUnZIKIxmRfpe_MPmihJ4otpJdQXEpRbgGefDwx48KCb-wTr5LAKBnxVztiQxVGgpcyXX-T6th7xuVdl-W939nbiQXYnk6i2QylUA2_fe0_XMBo" },
    @{ name = "17_editor_review_user_profile"; url = "https://lh3.googleusercontent.com/aida/AOfcidW7kElSj8BNpF2mT56Yk4IYUcQD8BrrdLuZ705cSNjE6aCiTg-MysXOycygKH8LyNW_RVCFSCn6N0rN6S0LBkAqWu51dyoaOWyCPcNSoN-HzXjSIdefx0k-oF3UVveSjvRyHOysvBOycxzrHrlMZ6CeTTtIfh-N9_7OZiuoLhulIZP8D86xIT7IIip5J70LHR7I3RLzWAcvHFBVZ-0Isom27PhANi2XQw5zJZ5yuB536Sbui-NLFL92mtw" }
)

foreach ($s in $screens) {
    $outFile = ".\design\screens\" + $s.name + ".png"
    Invoke-WebRequest -Uri $s.url -OutFile $outFile -UseBasicParsing
    Write-Host "Downloaded: $($s.name)" -ForegroundColor Green
}
Write-Host "Done!" -ForegroundColor Cyan
