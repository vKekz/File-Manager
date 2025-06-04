import { Component, OnInit, signal, WritableSignal } from "@angular/core";
import { IconComponent } from "../icon/icon.component";
import { delay } from "../../../helpers/timeout.helper";
import { ButtonComponent } from "../button/button.component";
import { Router } from "@angular/router";
import { AuthController } from "../../../controllers/auth.controller";
import { DASHBOARD_ROUTE, LOGIN_ROUTE } from "../../../constants/route.constants";

@Component({
  selector: "app-home",
  imports: [IconComponent, ButtonComponent],
  templateUrl: "./home.component.html",
  styleUrl: "./home.component.css",
})
export class HomeComponent implements OnInit {
  protected readonly iconSize: number = 128;
  protected readonly brandName: WritableSignal<string> = signal("S");

  constructor(
    private readonly router: Router,
    protected readonly authController: AuthController
  ) {}

  async ngOnInit() {
    await this.animateBrandName();
  }

  protected goToLogin() {
    return this.router.navigateByUrl(`/${LOGIN_ROUTE}`);
  }

  protected goToDashboard() {
    return this.router.navigateByUrl(`/${DASHBOARD_ROUTE}`);
  }

  private async animateBrandName() {
    const finalTitle = "Shelfy";
    const length = finalTitle.length;

    for (let i = 1; i < length; i++) {
      await delay(175);
      this.brandName.update((value) => value + finalTitle[i]);
    }
  }
}
