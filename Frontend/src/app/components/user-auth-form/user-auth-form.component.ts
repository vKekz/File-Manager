import { Component, Input } from "@angular/core";
import { AuthFormEnum } from "../../../enums/auth-form.enum";
import { ButtonComponent } from "../button/button.component";
import { IconComponent } from "../icon/icon.component";
import { Router } from "@angular/router";

@Component({
  selector: "app-user-auth-form",
  imports: [ButtonComponent, IconComponent],
  templateUrl: "./user-auth-form.component.html",
  styleUrl: "./user-auth-form.component.css",
})
export class UserAuthFormComponent {
  @Input()
  public type: AuthFormEnum = AuthFormEnum.LOGIN;

  protected readonly iconSize: number = 64;
  protected readonly AuthFormEnum = AuthFormEnum;

  constructor(private readonly router: Router) {}

  protected async goToOtherAuthForm() {
    const route = this.type == AuthFormEnum.LOGIN ? "/signup" : "/login";
    await this.router.navigateByUrl(route);
  }
}
